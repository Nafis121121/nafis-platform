<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ثبت درخواست سورسینگ | نفیس تجارت</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-3xl px-6 py-12">
        <a href="{{ url('/') }}" class="text-sm text-amber-300 hover:text-amber-200">بازگشت به سایت</a>
        <section class="mt-6 rounded-2xl bg-white p-6 text-slate-900 shadow-2xl sm:p-10">
            <h1 class="text-2xl font-bold">ثبت درخواست سورسینگ کالا</h1>
            <p class="mt-2 text-sm text-slate-600">مشخصات کالای موردنظر را ارسال کنید تا کارشناسان ما آن را بررسی کنند.</p>

            @if ($errors->any())
                <div class="mt-6 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('sourcing.submit') }}" method="post" enctype="multipart/form-data" class="mt-8 space-y-5">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium">نام و نام خانوادگی</span>
                        <input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium">ایمیل یا شماره موبایل</span>
                        <input name="contact" value="{{ old('contact') }}" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2" />
                    </label>
                </div>
                <label class="block">
                    <span class="text-sm font-medium">رمز عبور (برای حساب جدید اختیاری است)</span>
                    <input type="password" name="password" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2" />
                    <input type="password" name="password_confirmation" placeholder="تکرار رمز عبور" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2" />
                </label>
                <label class="block">
                    <span class="text-sm font-medium">عنوان کالای موردنظر</span>
                    <input name="title" value="{{ old('title') }}" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2" />
                </label>
                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium">لینک علی‌بابا یا ۱۶۸۸</span>
                        <input type="url" name="source_url" value="{{ old('source_url') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium">تیراژ تخمینی</span>
                        <input type="number" min="1" name="estimated_quantity" value="{{ old('estimated_quantity') }}" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2" />
                    </label>
                </div>
                <label class="block">
                    <span class="text-sm font-medium">توضیحات فنی</span>
                    <textarea name="technical_specifications" rows="5" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('technical_specifications') }}</textarea>
                </label>
                <label class="block">
                    <span class="text-sm font-medium">تصویر یا فایل نمونه (حداکثر ۵ مگابایت)</span>
                    <input type="file" name="sample" accept="image/*" class="mt-2 block w-full text-sm" />
                </label>
                <button type="submit" class="w-full rounded-lg bg-amber-500 px-5 py-3 font-bold text-slate-950 hover:bg-amber-400">ثبت درخواست و ورود به پورتال</button>
            </form>
        </section>
    </main>
</body>
</html>
