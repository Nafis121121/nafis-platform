# Nafis B2B Trading Platform - AI Development Rules

1. Stack & Architecture:
   - Laravel 11, Filament v3, Livewire 3, Tailwind CSS.
   - Admin Panel at `/admin` (strictly for staff roles: super_admin, sales_manager, warehouse_staff, content_manager).
   - Customer Portal at `/portal` (strictly for customer role; all queries must be scoped via `user_id = auth()->id()`).
   - Pure B2B commerce: never expose supplier costs, base prices, or internal margins to customer views or portals.

2. Formatting & Number Standards (MANDATORY):
   - ALWAYS format all numbers, quantities, prices, currencies, and financial totals with standard 3-digit comma separators (e.g., 10,000,000).
   - In Filament tables and infolists, always append `->numeric()` or format via `number_format()`.
   - In Blade templates and PDF views, wrap every numeric amount inside `number_format($value)`.

3. Persian & RTL Standards:
   - All Filament labels, placeholders, navigation groups, and action titles must be in fluent Persian (Farsi).
   - Ensure all Blade templates and PDF invoices include `dir="rtl"` with proper Persian typography and embedded fonts (e.g., Vazirmatn).

4. Code Quality & Scope:
   - Keep changes surgical and strictly scoped to the target files.
   - Never output binary stream data directly into Livewire responses; handle PDF downloads via `response()->streamDownload()`.
   - Ensure all feature tests continue to pass after any code modification.