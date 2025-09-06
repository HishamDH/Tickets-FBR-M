<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Change the current language
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang(Request $request)
    {
        // Validate the incoming language parameter
        $validLocale = in_array($request->lang, ['en', 'ar']);
        
        if ($validLocale) {
            // Store the selected language in session
            Session::put('locale', $request->lang);
        }
        
        // Redirect back to the previous page
        return back();
    }
}
