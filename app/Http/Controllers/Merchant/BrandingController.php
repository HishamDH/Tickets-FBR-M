<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BrandingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'merchant']);
    }

    /**
     * Show branding management form
     */
    public function index()
    {
        $merchant = Auth::user();
        
        return view('merchant.branding.index', [
            'merchant' => $merchant,
            'branding' => $merchant->branding ?? [],
            'socialLinks' => $merchant->social_links ?? [],
            'businessHours' => $merchant->business_hours ?? $this->getDefaultBusinessHours(),
        ]);
    }

    /**
     * Update subdomain settings
     */
    public function updateSubdomain(Request $request)
    {
        $merchant = Auth::user();

        $request->validate([
            'subdomain' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('users', 'subdomain')->ignore($merchant->id),
                'not_in:www,api,admin,app,mail,ftp,blog,shop,store,help,support,test,dev,staging'
            ]
        ], [
            'subdomain.regex' => 'Subdomain can only contain lowercase letters, numbers, and hyphens.',
            'subdomain.not_in' => 'This subdomain is reserved and cannot be used.',
        ]);

        $merchant->update([
            'subdomain' => strtolower($request->subdomain)
        ]);

        return back()->with('success', 'Subdomain updated successfully! Your store is now available at: ' . $request->subdomain . '.' . config('app.main_domain'));
    }

    /**
     * Update branding settings
     */
    public function updateBranding(Request $request)
    {
        $merchant = Auth::user();

        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'font_family' => 'nullable|string|in:Arial,Helvetica,Georgia,Times,Verdana,Roboto,Open Sans,Lato,Montserrat',
            'store_description' => 'nullable|string|max:500',
            'welcome_message' => 'nullable|string|max:200',
            'footer_text' => 'nullable|string|max:200',
        ]);

        $updateData = [];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($merchant->logo_url) {
                $oldPath = str_replace('/storage/', '', $merchant->logo_url);
                Storage::disk('public')->delete($oldPath);
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
            $updateData['logo_url'] = '/storage/' . $logoPath;
        }

        // Update branding data
        $branding = $merchant->branding ?? [];
        
        if ($request->filled('primary_color')) {
            $branding['primary_color'] = $request->primary_color;
        }
        
        if ($request->filled('secondary_color')) {
            $branding['secondary_color'] = $request->secondary_color;
        }
        
        if ($request->filled('accent_color')) {
            $branding['accent_color'] = $request->accent_color;
        }
        
        if ($request->filled('font_family')) {
            $branding['font_family'] = $request->font_family;
        }
        
        if ($request->filled('welcome_message')) {
            $branding['welcome_message'] = $request->welcome_message;
        }
        
        if ($request->filled('footer_text')) {
            $branding['footer_text'] = $request->footer_text;
        }

        $updateData['branding'] = $branding;

        if ($request->filled('store_description')) {
            $updateData['store_description'] = $request->store_description;
        }

        $merchant->update($updateData);

        return back()->with('success', 'Branding settings updated successfully!');
    }

    /**
     * Update social links
     */
    public function updateSocialLinks(Request $request)
    {
        $merchant = Auth::user();

        $request->validate([
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'twitter' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'youtube' => 'nullable|url',
            'tiktok' => 'nullable|url',
            'whatsapp' => 'nullable|string|max:20',
        ]);

        $socialLinks = array_filter([
            'facebook' => $request->facebook,
            'instagram' => $request->instagram,
            'twitter' => $request->twitter,
            'linkedin' => $request->linkedin,
            'youtube' => $request->youtube,
            'tiktok' => $request->tiktok,
            'whatsapp' => $request->whatsapp,
        ]);

        $merchant->update([
            'social_links' => $socialLinks
        ]);

        return back()->with('success', 'Social links updated successfully!');
    }

    /**
     * Update business hours
     */
    public function updateBusinessHours(Request $request)
    {
        $merchant = Auth::user();

        $request->validate([
            'monday_open' => 'nullable|date_format:H:i',
            'monday_close' => 'nullable|date_format:H:i',
            'tuesday_open' => 'nullable|date_format:H:i',
            'tuesday_close' => 'nullable|date_format:H:i',
            'wednesday_open' => 'nullable|date_format:H:i',
            'wednesday_close' => 'nullable|date_format:H:i',
            'thursday_open' => 'nullable|date_format:H:i',
            'thursday_close' => 'nullable|date_format:H:i',
            'friday_open' => 'nullable|date_format:H:i',
            'friday_close' => 'nullable|date_format:H:i',
            'saturday_open' => 'nullable|date_format:H:i',
            'saturday_close' => 'nullable|date_format:H:i',
            'sunday_open' => 'nullable|date_format:H:i',
            'sunday_close' => 'nullable|date_format:H:i',
            'monday_closed' => 'boolean',
            'tuesday_closed' => 'boolean',
            'wednesday_closed' => 'boolean',
            'thursday_closed' => 'boolean',
            'friday_closed' => 'boolean',
            'saturday_closed' => 'boolean',
            'sunday_closed' => 'boolean',
        ]);

        $businessHours = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if ($request->get($day . '_closed')) {
                $businessHours[$day] = 'closed';
            } else {
                $open = $request->get($day . '_open');
                $close = $request->get($day . '_close');
                
                if ($open && $close) {
                    $businessHours[$day] = $open . '-' . $close;
                } else {
                    $businessHours[$day] = 'closed';
                }
            }
        }

        $merchant->update([
            'business_hours' => $businessHours
        ]);

        return back()->with('success', 'Business hours updated successfully!');
    }

    /**
     * Update SEO settings
     */
    public function updateSeo(Request $request)
    {
        $merchant = Auth::user();

        $request->validate([
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $merchant->update([
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ]);

        return back()->with('success', 'SEO settings updated successfully!');
    }

    /**
     * Toggle store active status
     */
    public function toggleStore(Request $request)
    {
        $merchant = Auth::user();

        $merchant->update([
            'store_active' => !$merchant->store_active
        ]);

        $status = $merchant->store_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Store has been {$status} successfully!");
    }

    /**
     * Preview store
     */
    public function preview()
    {
        $merchant = Auth::user();

        if (!$merchant->subdomain) {
            return back()->with('error', 'Please set up your subdomain first.');
        }

        $previewUrl = 'http://' . $merchant->subdomain . '.' . config('app.main_domain');
        
        return redirect()->away($previewUrl);
    }

    /**
     * Check subdomain availability
     */
    public function checkSubdomain(Request $request)
    {
        $subdomain = strtolower($request->subdomain);
        
        // Reserved subdomains
        $reserved = ['www', 'api', 'admin', 'app', 'mail', 'ftp', 'blog', 'shop', 'store', 'help', 'support', 'test', 'dev', 'staging'];
        
        if (in_array($subdomain, $reserved)) {
            return response()->json([
                'available' => false,
                'message' => 'This subdomain is reserved.'
            ]);
        }

        // Check if already taken
        $exists = \App\Models\User::where('subdomain', $subdomain)
            ->where('id', '!=', Auth::id())
            ->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This subdomain is already taken.' : 'This subdomain is available.'
        ]);
    }

    /**
     * Get default business hours
     */
    private function getDefaultBusinessHours()
    {
        return [
            'monday' => '09:00-18:00',
            'tuesday' => '09:00-18:00',
            'wednesday' => '09:00-18:00',
            'thursday' => '09:00-18:00',
            'friday' => '09:00-18:00',
            'saturday' => '10:00-16:00',
            'sunday' => 'closed'
        ];
    }
}
