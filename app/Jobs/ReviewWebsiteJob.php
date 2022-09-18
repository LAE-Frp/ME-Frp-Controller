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
use Illuminate\Support\Facades\Storage;

class ReviewWebsiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        Host::with('server')->whereIn('protocol', ['http', 'https', 'tcp'])->chunk(100, function ($hosts) {
            foreach ($hosts as $host) {
                // if protocol is tcp
                if ($host->protocol == 'tcp') {

                    if ($this->getMark($host->id, 'not_http')) {
                        continue;
                    }
                    // 检测是不是 HTTP 服务
                    $url = 'http://' . $host->server->server_address . ':' . $host->remote_port;

                    $this->print('正在检测 TCP: ' . $url);
                    try {
                        $http = Http::timeout(3)->connectTimeout(3)->throw()->get($url);

                        // if successful
                        if ($http->successful()) {
                            $this->print('连接成功。');

                            $this->takeScreenshot($host->id, $url);
                        }
                    } catch (Exception) {
                        $this->mark($host->id, 'not_http');
                        continue;
                    }
                } else {
                    $url = $host->protocol . '://' . $host->custom_domain;

                    try {
                        $http = Http::timeout(3)->connectTimeout(3)->get($url);

                        // if successful
                        if ($http->successful()) {
                            $this->takeScreenshot($host->id, $url);
                        }
                    } catch (Exception) {
                        $this->mark($host->id, 'request_failed');
                        continue;
                    }
                }
            }
        });
    }

    private function print($msg)
    {
        echo $msg . PHP_EOL;
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
        } catch (Exception $e) {
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

        $this->mark($host_id, 'success');
    }


    public function mark($host_id, $status)
    {
        Cache::put('host_review_' . $host_id, $status, now()->addDays(7));
    }

    public function getMark($host_id, $status) {
        return Cache::get('host_review_' . $host_id, null) === $status ? $status : 'success';
    }
}
