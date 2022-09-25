<?php

namespace App\Jobs;

use Exception;
use App\Models\Host;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Facebook\WebDriver\WebDriverDimension;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReviewWebsiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $http;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        $this->http = Http::remote('remote')->asForm();

        Host::with('server')->where('status', 'running')->whereIn('protocol', ['http', 'https', 'tcp'])->chunk(100, function ($hosts) {
            foreach ($hosts as $host) {
                // if protocol is tcp
                if ($host->protocol == 'tcp') {

                    // if ($this->getMark($host->id, 'not_http')) {
                    //     continue;
                    // }
                    // 检测是不是 HTTP 服务

                    // if is is_china_mainland
                    if ($host->server->is_china_mainland) {
                        $url = 'http://' . $host->server->server_address . ':' . $host->remote_port;

                        $this->print('正在检测 TCP: ' . $url);

                        try {
                            $http = Http::timeout(3)->connectTimeout(3)->get($url);

                            // if header include text/html
                            if (strpos($http->header('Content-Type'), 'text/html') !== false) {
                                $this->print('检测到 TCP: ' . $url . ' 是 HTTP 服务，所以暂停了隧道。');

                                $this->http->patch('hosts/' . $host->host_id, [
                                    'status' => 'suspended',
                                ]);

                                // $host->delete();
                                // $host->protocol = 'http';
                                // $host->save();
                            }
                        } catch (Exception) {
                            continue;
                        }
                    }
                } else {
                    $url = $host->protocol . '://' . $host->custom_domain;

                    $this->print('正在检测 URL : ' . $url);
                    // 检测 CNAME 或 A 记录
                    $this->print('正在检测 CNAME 或 A 记录: ' . $host->custom_domain);

                    // 检测 DNS
                    $dns = dns_get_record($host->custom_domain, DNS_CNAME | DNS_A);

                    if (!$dns) {
                        $this->print('因为 ' . $host->custom_domain . ' 没有解析，所以暂停了隧道。');

                        $this->http->patch('hosts/' . $host->host_id, [
                            'status' => 'suspended',
                        ]);

                        continue;
                    }


                    $this->print('检测到 CNAME 或 A 记录: ' . $host->custom_domain);
                    $this->print('类型 ' . $dns[0]['type'] . ' 指向 ' . $dns[0]['ip']);

                    try {
                        $http = Http::timeout(3)->connectTimeout(3)->get($url);

                        // if successful
                        if ($http->successful()) {
                            $this->takeScreenshot($host->id, $url);
                        }
                    } catch (Exception) {
                        continue;
                    }
                }
            }
        });
    }

    private function print($msg)
    {
        echo $msg . PHP_EOL;
        Log::info($msg);
    }

    private function takeScreenshot($host_id, $url)
    {
        $this->print('正在拉取屏幕截图: ' . $url);

        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments([
            '--headless', '--sandbox', '--window-size=1024,768', '--ignore-certificate-errors'
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);
        $driver = RemoteWebDriver::create(config('app.webdriver_host'), $capabilities);

        $driver->get($url);

        // if has alert, accept
        try {
            $driver->switchTo()->alert()->accept();
        } catch (Exception) {
            // no alert
        }

        // wait page
        $driver->wait(10, 1000)->until(
            function () use ($driver) {
                return $driver->executeScript('return document.readyState') == 'complete';
            }
        );

        sleep(1);

        // get web content
        $webContent = $driver->getPageSource();

        $width = $driver->executeScript("return document.documentElement.scrollWidth");
        $height = $driver->executeScript("return document.documentElement.scrollHeight");

        // set window size
        $driver->manage()->window()->setSize(new WebDriverDimension($width, $height));

        $screenshotData = $driver->takeScreenshot();


        $driver->manage()->deleteAllCookies();

        $driver->quit();

        $today = date('Y-m-d');

        Storage::disk('public')->put('reviews/' . $today . '/screenshots/' . $host_id . '.png', $screenshotData);
        Storage::disk('public')->put('reviews/' . $today . '/contents/' . $host_id . '.txt', $webContent);

        // $this->mark($host_id, 'success');
    }


    // public function mark($host_id, $status)
    // {
    //     Cache::put('host_review_' . $host_id, $status, now()->addDays(7));
    // }

    // public function getMark($host_id, $status)
    // {
    //     return Cache::get('host_review_' . $host_id, null) === $status ? $status : 'success';
    // }
}
