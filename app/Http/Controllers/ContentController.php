<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['show', 'index']);
    }

    public function index()
    {
        $contents = $this->getContentFromFiles();
        return view('content.index', compact('contents'));
    }

    public function show($slug)
    {
        $content = $this->getContentBySlug($slug);
        
        if (!$content) {
            abort(404, 'المحتوى غير موجود');
        }

        return view('content.show', compact('content'));
    }

    public function adminIndex()
    {
        $contents = $this->getContentFromFiles();
        $stats = [
            'total_pages' => count($contents),
            'published' => count(array_filter($contents, fn($c) => $c['status'] === 'published')),
            'draft' => count(array_filter($contents, fn($c) => $c['status'] === 'draft')),
        ];

        return view('admin.content.index', compact('contents', 'stats'));
    }

    public function create()
    {
        return view('admin.content.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'slug' => 'nullable|string|max:255|unique:content_files,slug',
            'meta_description' => 'nullable|string|max:160',
            'status' => 'required|in:draft,published',
            'type' => 'required|in:page,article,faq,policy'
        ]);

        $slug = $request->slug ?: Str::slug($request->title);
        
        // Check if slug already exists
        if ($this->slugExists($slug)) {
            return back()->withErrors(['slug' => 'هذا الرابط مستخدم بالفعل'])->withInput();
        }

        $content = [
            'title' => $request->title,
            'content' => $request->content,
            'slug' => $slug,
            'meta_description' => $request->meta_description,
            'status' => $request->status,
            'type' => $request->type,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
            'author' => auth()->user()->name,
        ];

        $this->saveContentToFile($slug, $content);

        return redirect()->route('admin.content.index')->with('success', 'تم إنشاء المحتوى بنجاح');
    }

    public function edit($slug)
    {
        $content = $this->getContentBySlug($slug);
        
        if (!$content) {
            abort(404, 'المحتوى غير موجود');
        }

        return view('admin.content.edit', compact('content'));
    }

    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:160',
            'status' => 'required|in:draft,published',
            'type' => 'required|in:page,article,faq,policy'
        ]);

        $content = $this->getContentBySlug($slug);
        
        if (!$content) {
            abort(404, 'المحتوى غير موجود');
        }

        $content['title'] = $request->title;
        $content['content'] = $request->content;
        $content['meta_description'] = $request->meta_description;
        $content['status'] = $request->status;
        $content['type'] = $request->type;
        $content['updated_at'] = now()->toISOString();

        $this->saveContentToFile($slug, $content);

        return redirect()->route('admin.content.index')->with('success', 'تم تحديث المحتوى بنجاح');
    }

    public function destroy($slug)
    {
        if (!$this->contentFileExists($slug)) {
            abort(404, 'المحتوى غير موجود');
        }

        Storage::disk('local')->delete("content/{$slug}.json");

        return redirect()->route('admin.content.index')->with('success', 'تم حذف المحتوى بنجاح');
    }

    // File-based content management methods
    private function getContentFromFiles()
    {
        if (!Storage::disk('local')->exists('content')) {
            Storage::disk('local')->makeDirectory('content');
        }

        $files = Storage::disk('local')->files('content');
        $contents = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $content = json_decode(Storage::disk('local')->get($file), true);
                if ($content) {
                    $contents[] = $content;
                }
            }
        }

        return collect($contents)->sortByDesc('updated_at')->values()->all();
    }

    private function getContentBySlug($slug)
    {
        $filePath = "content/{$slug}.json";
        
        if (!Storage::disk('local')->exists($filePath)) {
            return null;
        }

        return json_decode(Storage::disk('local')->get($filePath), true);
    }

    private function saveContentToFile($slug, $content)
    {
        if (!Storage::disk('local')->exists('content')) {
            Storage::disk('local')->makeDirectory('content');
        }

        Storage::disk('local')->put("content/{$slug}.json", json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function contentFileExists($slug)
    {
        return Storage::disk('local')->exists("content/{$slug}.json");
    }

    private function slugExists($slug)
    {
        return $this->contentFileExists($slug);
    }

    // API methods for dynamic content
    public function getContent($slug)
    {
        $content = $this->getContentBySlug($slug);
        
        if (!$content || $content['status'] !== 'published') {
            return response()->json(['error' => 'المحتوى غير موجود'], 404);
        }

        return response()->json($content);
    }

    public function searchContent(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type');
        
        $contents = $this->getContentFromFiles();
        
        $filtered = collect($contents)->filter(function($content) use ($query, $type) {
            // Filter by status
            if ($content['status'] !== 'published') {
                return false;
            }
            
            // Filter by type
            if ($type && $content['type'] !== $type) {
                return false;
            }
            
            // Search in title and content
            if ($query) {
                return Str::contains(Str::lower($content['title']), Str::lower($query)) ||
                       Str::contains(Str::lower($content['content']), Str::lower($query));
            }
            
            return true;
        });

        return view('content.search', compact('filtered', 'query', 'type'));
    }

    // Predefined content creation
    public function createPredefinedContent()
    {
        $predefinedContent = [
            [
                'title' => 'سياسة الخصوصية',
                'slug' => 'privacy-policy',
                'type' => 'policy',
                'content' => $this->getPrivacyPolicyContent(),
                'status' => 'published',
            ],
            [
                'title' => 'شروط الاستخدام',
                'slug' => 'terms-of-service',
                'type' => 'policy',
                'content' => $this->getTermsOfServiceContent(),
                'status' => 'published',
            ],
            [
                'title' => 'الأسئلة الشائعة',
                'slug' => 'faq',
                'type' => 'faq',
                'content' => $this->getFAQContent(),
                'status' => 'published',
            ],
            [
                'title' => 'عن المنصة',
                'slug' => 'about-us',
                'type' => 'page',
                'content' => $this->getAboutUsContent(),
                'status' => 'published',
            ]
        ];

        $created = 0;
        foreach ($predefinedContent as $content) {
            if (!$this->slugExists($content['slug'])) {
                $content['created_at'] = now()->toISOString();
                $content['updated_at'] = now()->toISOString();
                $content['author'] = auth()->user()->name;
                $content['meta_description'] = Str::limit(strip_tags($content['content']), 160);
                
                $this->saveContentToFile($content['slug'], $content);
                $created++;
            }
        }

        return redirect()->route('admin.content.index')->with('success', "تم إنشاء {$created} صفحة من المحتوى المحدد مسبقاً");
    }

    // Content templates
    private function getPrivacyPolicyContent()
    {
        return '<h2>سياسة الخصوصية</h2>
        <p>نحن في منصة الحجوزات نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية.</p>
        <h3>البيانات التي نجمعها</h3>
        <ul>
            <li>المعلومات الشخصية (الاسم، البريد الإلكتروني، رقم الهاتف)</li>
            <li>معلومات الحجوزات والمدفوعات</li>
            <li>بيانات الاستخدام والتفضيلات</li>
        </ul>
        <h3>كيف نستخدم بياناتك</h3>
        <ul>
            <li>تقديم الخدمات المطلوبة</li>
            <li>التواصل معك بخصوص الحجوزات</li>
            <li>تحسين جودة الخدمة</li>
        </ul>';
    }

    private function getTermsOfServiceContent()
    {
        return '<h2>شروط الاستخدام</h2>
        <p>باستخدام منصة الحجوزات، فإنك توافق على الشروط والأحكام التالية:</p>
        <h3>قواعد الاستخدام</h3>
        <ul>
            <li>يجب استخدام المنصة للأغراض المشروعة فقط</li>
            <li>عدم انتهاك حقوق الملكية الفكرية</li>
            <li>احترام قوانين وأنظمة المملكة العربية السعودية</li>
        </ul>
        <h3>المسؤوليات</h3>
        <ul>
            <li>المنصة وسيط بين مقدمي الخدمات والعملاء</li>
            <li>جودة الخدمة مسؤولية مقدم الخدمة</li>
            <li>النزاعات تحل وفقاً للأنظمة المعمول بها</li>
        </ul>';
    }

    private function getFAQContent()
    {
        return '<h2>الأسئلة الشائعة</h2>
        <div class="faq-item">
            <h3>كيف يمكنني حجز خدمة؟</h3>
            <p>يمكنك تصفح الخدمات المتاحة واختيار الخدمة المناسبة، ثم اتباع خطوات الحجز وإدخال البيانات المطلوبة.</p>
        </div>
        <div class="faq-item">
            <h3>هل يمكنني إلغاء الحجز؟</h3>
            <p>نعم، يمكنك إلغاء الحجز قبل 24 ساعة من موعد الخدمة مع إمكانية استرداد كامل المبلغ.</p>
        </div>
        <div class="faq-item">
            <h3>كيف يتم الدفع؟</h3>
            <p>نوفر عدة طرق دفع آمنة بما في ذلك البطاقات الائتمانية والدفع عند تقديم الخدمة.</p>
        </div>';
    }

    private function getAboutUsContent()
    {
        return '<h2>عن منصة الحجوزات</h2>
        <p>منصة الحجوزات هي أول منصة سعودية متخصصة في ربط مقدمي الخدمات بالعملاء من خلال نظام حجز إلكتروني متطور.</p>
        <h3>رؤيتنا</h3>
        <p>أن نكون المنصة الرائدة في المملكة العربية السعودية لحجز الخدمات الإلكترونية.</p>
        <h3>مهمتنا</h3>
        <p>تسهيل عملية الوصول للخدمات وتوفير تجربة حجز سلسة ومريحة للجميع.</p>
        <h3>قيمنا</h3>
        <ul>
            <li>الجودة والامتياز في الخدمة</li>
            <li>الشفافية والمصداقية</li>
            <li>الابتكار والتطوير المستمر</li>
        </ul>';
    }
}
