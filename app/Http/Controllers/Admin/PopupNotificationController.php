<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PopupNotificationController extends Controller
{
    public function index()
    {
        $notification = \App\Config::where("name", "popupnotification")->first();
        return view("admin.popupnotifiaction", compact("notification"));
    }
    private function EnValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $str .= "\n";
        $keyPosition = strpos($str, $envKey . "=");
        $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
        $str = str_replace($oldLine, $envKey . "=" . $envValue, $str);
        $str = substr($str, 0, -1);
        $fp = fopen($envFile, "w");
        fwrite($fp, $str);
        fclose($fp);
    }
    public function update(\Illuminate\Http\Request $request)
    {
        $notification = \App\Config::where("name", "popupnotification")->first();
        $notification->value = $request->description;
        $notification->save();
        $this->EnValue("Popup_Notifications", $request->status);
        \Illuminate\Support\Facades\Session::flash("alert", __("messages.updated"));
        \Illuminate\Support\Facades\Session::flash("alertClass", "success");
        return redirect("/admin/popup-notification");
    }
}
