<?php

namespace App\Http\Controllers\Remote;

use App\Http\Controllers\Controller;
use App\Models\Host;
use Illuminate\Http\Request;

class HostController extends Controller
{
    public function update(Request $request, Host $host)
    {
        //

        switch ($request->status) {
            case 'running':
                $this->http->post('/tasks', [
                    'title' => '正在解除暂停。',
                    'host_id' => $host->id,
                    'status' => 'done',
                ])->json();

                $host->status = 'running';
                $host->save();

                return $this->success($host);

                break;

            case 'suspended':

                // 如果主机被暂停，则代表主机进入待删除状态。
                // 这个操作不能被用户调用，所以要判断是否是平台调用。

                // 执行暂停操作，然后标记为暂停状态

                // 检测是不是平台调用

                $host->update($request->all());

                // 执行一系列暂停操作

                $this->http->post('/tasks', [
                    'title' => '服务器已暂停。',
                    'host_id' => $host->id,
                    'status' => 'done',
                ])->json();

                break;

            case 'error':
                $host->update($request->all());

                break;
        }

        $host->update($request->all());

        return $this->updated($host);
    }

    public function destroy(Host $host)
    {
        // 如果你想要拥有自己的一套删除逻辑，可以不处理这个。返回 false 即可。
        return false;


        // 或者执行 Functions/HostController.php 中的 destroy 方法。

        $HostController = new Functions\HostController();

        return $HostController->destroy($host);

    }
}
