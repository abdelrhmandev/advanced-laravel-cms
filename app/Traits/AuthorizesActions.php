<?php
namespace App\Traits;
use Illuminate\Support\Str;
trait AuthorizesActions
{
    public function authorizeAction($method = null)
    {
        $method = $method ?? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        $ignored = ['mount', 'render'];
        if (in_array($method, $ignored)) {
            return;
        }
        $class = get_class($this);
        $module = 'global';

        if (preg_match('/\\\\CMS\\\\([^\\\\]+)/', $class, $matches)) {
            $module = \Illuminate\Support\Str::snake($matches[1]);
        }
        $permission = "{$module}." . strtolower($method);
        abort_unless(auth('admin')->user()->can($permission), 403, 'Unauthorized');
    }
}
