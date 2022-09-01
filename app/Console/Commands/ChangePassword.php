<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class ChangePassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pwd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修改指定管理员用户的密码';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->ask('请输入邮箱');

        $admin = Admin::where('email', $email)->first();
        if (!$admin) {
            $this->error('用户不存在');
            return 1;
        }

        $password = $this->secret('请输入新密码');
        $admin->password = bcrypt($password);
        $admin->save();

        $this->info('密码修改成功');


        return 0;
    }
}
