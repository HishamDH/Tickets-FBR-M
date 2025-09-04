@extends('layouts.merchant')

@section('title', 'Store Branding & Settings')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Store Branding & Settings</h1>
        <div>
            @if($merchant->subdomain)
                <a href="{{ route('merchant.branding.preview') }}" target="_blank" class="btn btn-outline-primary me-2">
                    <i class="fas fa-external-link-alt"></i> Preview Store
                </a>
            @endif
            <button class="btn btn-success" onclick="toggleStore()">
                @if($merchant->store_active)
                    <i class="fas fa-pause"></i> Deactivate Store
                @else
                    <i class="fas fa-play"></i> Activate Store
                @endif
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Subdomain Configuration -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link text-primary me-2"></i>Subdomain Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('merchant.branding.subdomain.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="subdomain" class="form-label">Store Subdomain <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('subdomain') is-invalid @enderror" 
                                       id="subdomain" name="subdomain" value="{{ old('subdomain', $merchant->subdomain) }}"
                                       placeholder="your-store-name" onblur="checkSubdomain()">
                                <span class="input-group-text">.{{ config('app.main_domain') }}</span>
                            </div>
                            <div id="subdomain-feedback" class="mt-2"></div>
                            @error('subdomain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Your store will be accessible at: <strong id="store-url">
                                    {{ $merchant->subdomain ?? 'your-store-name' }}.{{ config('app.main_domain') }}
                                </strong>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Subdomain
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Store Status -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-store text-success me-2"></i>Store Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="badge badge-{{ $merchant->store_active ? 'success' : 'danger' }} fs-6">
                                {{ $merchant->store_active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0">
                                @if($merchant->store_active)
                                    Your store is live and accepting customers
                                @else
                                    Your store is currently offline
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($merchant->subdomain)
                        <hr>
                        <small class="text-muted">
                            Store URL: <a href="http://{{ $merchant->subdomain }}.{{ config('app.main_domain') }}" target="_blank">
                                {{ $merchant->subdomain }}.{{ config('app.main_domain') }}
                            </a>
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Branding Settings -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="brandingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="visual-tab" data-bs-toggle="tab" data-bs-target="#visual" type="button" role="tab">
                                <i class="fas fa-palette"></i> Visual Identity
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button" role="tab">
                                <i class="fas fa-edit"></i> Content & Description
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                                <i class="fas fa-share-alt"></i> Social Links
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hours-tab" data-bs-toggle="tab" data-bs-target="#hours" type="button" role="tab">
                                <i class="fas fa-clock"></i> Business Hours
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">
                                <i class="fas fa-search"></i> SEO Settings
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="brandingTabsContent">
                        <!-- Visual Identity Tab -->
                        <div class="tab-pane fade show active" id="visual" role="tabpanel">
                            <form action="{{ route('merchant.branding.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="logo" class="form-label">Store Logo</label>
                                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                                   id="logo" name="logo" accept="image/*">
                                            @error('logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if($merchant->logo_url)
                                                <div class="mt-2">
                                                    <img src="{{ $merchant->logo_url }}" alt="Current Logo" style="max-height: 80px;">
                                                    <small class="text-muted d-block">Current logo</small>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label for="primary_color" class="form-label">Primary Color</label>
                                            <input type="color" class="form-control form-control-color @error('primary_color') is-invalid @enderror" 
                                                   id="primary_color" name="primary_color" 
                                                   value="{{ old('primary_color', $branding['primary_color'] ?? '#007bff') }}">
                                            @error('primary_color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="secondary_color" class="form-label">Secondary Color</label>
                                            <input type="color" class="form-control form-control-color @error('secondary_color') is-invalid @enderror" 
                                                   id="secondary_color" name="secondary_color" 
                                                   value="{{ old('secondary_color', $branding['secondary_color'] ?? '#6c757d') }}">
                                            @error('secondary_color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="accent_color" class="form-label">Accent Color</label>
                                            <input type="color" class="form-control form-control-color @error('accent_color') is-invalid @enderror" 
                                                   id="accent_color" name="accent_color" 
                                                   value="{{ old('accent_color', $branding['accent_color'] ?? '#28a745') }}">
                                            @error('accent_color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="font_family" class="form-label">Font Family</label>
                                            <select class="form-select @error('font_family') is-invalid @enderror" 
                                                    id="font_family" name="font_family">
                                                <option value="Arial" {{ old('font_family', $branding['font_family'] ?? '') == 'Arial' ? 'selected' : '' }}>Arial</option>
                                                <option value="Helvetica" {{ old('font_family', $branding['font_family'] ?? '') == 'Helvetica' ? 'selected' : '' }}>Helvetica</option>
                                                <option value="Georgia" {{ old('font_family', $branding['font_family'] ?? '') == 'Georgia' ? 'selected' : '' }}>Georgia</option>
                                                <option value="Times" {{ old('font_family', $branding['font_family'] ?? '') == 'Times' ? 'selected' : '' }}>Times</option>
                                                <option value="Verdana" {{ old('font_family', $branding['font_family'] ?? '') == 'Verdana' ? 'selected' : '' }}>Verdana</option>
                                                <option value="Roboto" {{ old('font_family', $branding['font_family'] ?? '') == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                                <option value="Open Sans" {{ old('font_family', $branding['font_family'] ?? '') == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                                <option value="Lato" {{ old('font_family', $branding['font_family'] ?? '') == 'Lato' ? 'selected' : '' }}>Lato</option>
                                                <option value="Montserrat" {{ old('font_family', $branding['font_family'] ?? '') == 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
                                            </select>
                                            @error('font_family')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="welcome_message" class="form-label">Welcome Message</label>
                                            <input type="text" class="form-control @error('welcome_message') is-invalid @enderror" 
                                                   id="welcome_message" name="welcome_message" 
                                                   value="{{ old('welcome_message', $branding['welcome_message'] ?? '') }}"
                                                   placeholder="Welcome to our store!">
                                            @error('welcome_message')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="footer_text" class="form-label">Footer Text</label>
                                            <input type="text" class="form-control @error('footer_text') is-invalid @enderror" 
                                                   id="footer_text" name="footer_text" 
                                                   value="{{ old('footer_text', $branding['footer_text'] ?? '') }}"
                                                   placeholder="© 2024 Your Store Name. All rights reserved.">
                                            @error('footer_text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Visual Identity
                                </button>
                            </form>
                        </div>

                        <!-- Content & Description Tab -->
                        <div class="tab-pane fade" id="content" role="tabpanel">
                            <form action="{{ route('merchant.branding.update') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="store_description" class="form-label">Store Description</label>
                                    <textarea class="form-control @error('store_description') is-invalid @enderror" 
                                              id="store_description" name="store_description" rows="4"
                                              placeholder="Tell customers about your store, services, and what makes you special...">{{ old('store_description', $merchant->store_description) }}</textarea>
                                    @error('store_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">This description will appear on your store's homepage and about page.</div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Content
                                </button>
                            </form>
                        </div>

                        <!-- Social Links Tab -->
                        <div class="tab-pane fade" id="social" role="tabpanel">
                            <form action="{{ route('merchant.branding.social-links.update') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="facebook" class="form-label">
                                                <i class="fab fa-facebook text-primary"></i> Facebook
                                            </label>
                                            <input type="url" class="form-control @error('facebook') is-invalid @enderror" 
                                                   id="facebook" name="facebook" 
                                                   value="{{ old('facebook', $socialLinks['facebook'] ?? '') }}"
                                                   placeholder="https://facebook.com/yourpage">
                                            @error('facebook')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="instagram" class="form-label">
                                                <i class="fab fa-instagram text-danger"></i> Instagram
                                            </label>
                                            <input type="url" class="form-control @error('instagram') is-invalid @enderror" 
                                                   id="instagram" name="instagram" 
                                                   value="{{ old('instagram', $socialLinks['instagram'] ?? '') }}"
                                                   placeholder="https://instagram.com/yourprofile">
                                            @error('instagram')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="twitter" class="form-label">
                                                <i class="fab fa-twitter text-info"></i> Twitter
                                            </label>
                                            <input type="url" class="form-control @error('twitter') is-invalid @enderror" 
                                                   id="twitter" name="twitter" 
                                                   value="{{ old('twitter', $socialLinks['twitter'] ?? '') }}"
                                                   placeholder="https://twitter.com/yourhandle">
                                            @error('twitter')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="linkedin" class="form-label">
                                                <i class="fab fa-linkedin text-primary"></i> LinkedIn
                                            </label>
                                            <input type="url" class="form-control @error('linkedin') is-invalid @enderror" 
                                                   id="linkedin" name="linkedin" 
                                                   value="{{ old('linkedin', $socialLinks['linkedin'] ?? '') }}"
                                                   placeholder="https://linkedin.com/company/yourcompany">
                                            @error('linkedin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="youtube" class="form-label">
                                                <i class="fab fa-youtube text-danger"></i> YouTube
                                            </label>
                                            <input type="url" class="form-control @error('youtube') is-invalid @enderror" 
                                                   id="youtube" name="youtube" 
                                                   value="{{ old('youtube', $socialLinks['youtube'] ?? '') }}"
                                                   placeholder="https://youtube.com/c/yourchannel">
                                            @error('youtube')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="tiktok" class="form-label">
                                                <i class="fab fa-tiktok text-dark"></i> TikTok
                                            </label>
                                            <input type="url" class="form-control @error('tiktok') is-invalid @enderror" 
                                                   id="tiktok" name="tiktok" 
                                                   value="{{ old('tiktok', $socialLinks['tiktok'] ?? '') }}"
                                                   placeholder="https://tiktok.com/@yourusername">
                                            @error('tiktok')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="whatsapp" class="form-label">
                                                <i class="fab fa-whatsapp text-success"></i> WhatsApp
                                            </label>
                                            <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" 
                                                   id="whatsapp" name="whatsapp" 
                                                   value="{{ old('whatsapp', $socialLinks['whatsapp'] ?? '') }}"
                                                   placeholder="+1234567890">
                                            @error('whatsapp')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Enter phone number with country code</div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Social Links
                                </button>
                            </form>
                        </div>

                        <!-- Business Hours Tab -->
                        <div class="tab-pane fade" id="hours" role="tabpanel">
                            <form action="{{ route('merchant.branding.business-hours.update') }}" method="POST">
                                @csrf
                                <div class="row">
                                    @php
                                        $days = [
                                            'monday' => 'Monday',
                                            'tuesday' => 'Tuesday', 
                                            'wednesday' => 'Wednesday',
                                            'thursday' => 'Thursday',
                                            'friday' => 'Friday',
                                            'saturday' => 'Saturday',
                                            'sunday' => 'Sunday'
                                        ];
                                    @endphp
                                    
                                    @foreach($days as $day => $dayName)
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">{{ $dayName }}</h6>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   id="{{ $day }}_closed" name="{{ $day }}_closed" value="1"
                                                                   {{ ($businessHours[$day] ?? '') == 'closed' ? 'checked' : '' }}
                                                                   onchange="toggleDayHours('{{ $day }}')">
                                                            <label class="form-check-label" for="{{ $day }}_closed">Closed</label>
                                                        </div>
                                                    </div>
                                                    
                                                    <div id="{{ $day }}_hours" class="{{ ($businessHours[$day] ?? '') == 'closed' ? 'd-none' : '' }}">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <label for="{{ $day }}_open" class="form-label">Open</label>
                                                                <input type="time" class="form-control" 
                                                                       id="{{ $day }}_open" name="{{ $day }}_open"
                                                                       value="{{ ($businessHours[$day] ?? '') != 'closed' ? explode('-', $businessHours[$day] ?? '09:00-18:00')[0] ?? '' : '' }}">
                                                            </div>
                                                            <div class="col-6">
                                                                <label for="{{ $day }}_close" class="form-label">Close</label>
                                                                <input type="time" class="form-control" 
                                                                       id="{{ $day }}_close" name="{{ $day }}_close"
                                                                       value="{{ ($businessHours[$day] ?? '') != 'closed' ? explode('-', $businessHours[$day] ?? '09:00-18:00')[1] ?? '' : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Business Hours
                                </button>
                            </form>
                        </div>

                        <!-- SEO Settings Tab -->
                        <div class="tab-pane fade" id="seo" role="tabpanel">
                            <form action="{{ route('merchant.branding.seo.update') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                           id="meta_title" name="meta_title" 
                                           value="{{ old('meta_title', $merchant->meta_title) }}"
                                           placeholder="Your Store Name - Professional Services"
                                           maxlength="60">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Recommended length: 50-60 characters</div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              id="meta_description" name="meta_description" rows="3"
                                              placeholder="Brief description of your store and services for search engines..."
                                              maxlength="160">{{ old('meta_description', $merchant->meta_description) }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Recommended length: 150-160 characters</div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                           id="meta_keywords" name="meta_keywords" 
                                           value="{{ old('meta_keywords', $merchant->meta_keywords) }}"
                                           placeholder="service, booking, appointment, your-city, your-industry">
                                    @error('meta_keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Separate keywords with commas</div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update SEO Settings
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkSubdomain() {
    const subdomainInput = document.getElementById('subdomain');
    const feedbackDiv = document.getElementById('subdomain-feedback');
    const storeUrl = document.getElementById('store-url');
    const subdomain = subdomainInput.value.toLowerCase();
    
    if (!subdomain) {
        feedbackDiv.innerHTML = '';
        return;
    }
    
    // Update store URL preview
    storeUrl.textContent = subdomain + '.{{ config("app.main_domain") }}';
    
    fetch(`{{ route('merchant.branding.check-subdomain') }}?subdomain=${subdomain}`)
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                feedbackDiv.innerHTML = '<div class="text-success"><i class="fas fa-check"></i> ' + data.message + '</div>';
                subdomainInput.classList.remove('is-invalid');
                subdomainInput.classList.add('is-valid');
            } else {
                feedbackDiv.innerHTML = '<div class="text-danger"><i class="fas fa-times"></i> ' + data.message + '</div>';
                subdomainInput.classList.remove('is-valid');
                subdomainInput.classList.add('is-invalid');
            }
        })
        .catch(error => {
            console.error('Error checking subdomain:', error);
        });
}

function toggleDayHours(day) {
    const checkbox = document.getElementById(day + '_closed');
    const hoursDiv = document.getElementById(day + '_hours');
    
    if (checkbox.checked) {
        hoursDiv.classList.add('d-none');
    } else {
        hoursDiv.classList.remove('d-none');
    }
}

function toggleStore() {
    if (confirm('Are you sure you want to change your store status?')) {
        fetch('{{ route("merchant.branding.toggle-store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            location.reload();
        })
        .catch(error => {
            console.error('Error toggling store status:', error);
            alert('Error updating store status. Please try again.');
        });
    }
}

// Real-time subdomain input formatting
document.getElementById('subdomain').addEventListener('input', function(e) {
    this.value = this.value.toLowerCase().replace(/[^a-z0-9\-]/g, '');
});
</script>
@endsection
