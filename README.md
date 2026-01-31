# Jasper Reports for Laravel (NextERP)

A robust, enterprise-grade wrapper for Jasper Reports in Laravel, designed to seamlessly handle Java compatibility issues on modern systems (macOS Apple Silicon, Linux, etc.) by enforcing the use of Java 8.

## 🚀 Features

- **Java 8 Compatibility Engine**: Solves $ClassCastException$ issues by using a strictly defined local Java 8 binary.
- **Automated Binary Patching**: Automatically patches the underlying `jasperstarter` executable to use the local Java 8.
- **Enterprise Reports**: Includes pre-built templates for Trial Balance, Income Statement, Balance Sheet, Cash Flow, Changes in Equity, and General Ledger.

---

## 🛠 Step-by-Step Installation

Follow these steps to integrate Jasper Reports into your Laravel project.

### 1. Configure Repository
Add the package as a local path repository in your root `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "../jasper-reports"
    }
],
```

### 2. Install Package
Run the following command to add the package to your project:

```bash
composer require nexterp/jasper-reports:@dev
```

### 3. Run Automated Setup
This package includes a setup script that automates Java 8 installation and binary patching.

```bash
# Navigate to the package directory (or run from root if scripts are synced)
cd vendor/nexterp/jasper-reports
chmod +x setup-jasper.sh
./setup-jasper.sh
```
*Note: Default setup password is `nexterp123`.*

### 4. Publish Resources
Publish the report templates and views to your application:

```bash
php artisan vendor:publish --tag=jasper-reports-templates
```

### 5. Finalize Environment
Ensure your root `post-update-cmd.php` (if present) or `composer.json` scripts are configured to run patching on every `dump-autoload`. If you encounter Java errors, run:

```bash
composer dump-autoload
```

### 6. Rebuilding Reports
If you modify the report templates (`.jrxml` files), you must recompile them to update the binary `.jasper` files. Use the provided rebuild script:

```bash
php rebuild_all_v3.php
```
This will scan the report directory and recompile all templates to ensure your changes are reflected in the generated output.

---

## 💻 Usage

### Generating a Report
Inject `JasperReportService` into your controller:

```php
use Nexterp\JasperReports\JasperReportService;

public function __construct(JasperReportService $jasper) {
    $this->jasper = $jasper;
}

public function download() {
    $input = resource_path('reports/vendor/jasper-reports/trial_balance.jrxml');
    $output = storage_path('app/reports/trial_balance_'.time());
    $params = ['logo' => public_path('logo.png')];
    
    $path = $this->jasper->generateReport($input, $output, ['pdf'], $params);
    return response()->file($path);
}
```

---

## 📊 Included Reports
- **Trial Balance**: `trial_balance.jrxml`
- **Income Statement**: `income_statement.jrxml`
- **Balance Sheet**: `balance_sheet.jrxml`
- **Cash Flow**: `cash_flow.jrxml`
- **Changes in Equity**: `changes_in_equity.jrxml`
- **General Ledger**: `general_ledger.jrxml`

---

## 🔧 Troubleshooting

| Error | Cause | Solution |
|-------|-------|----------|
| `ClassCastException` | System Java (17+) is used. | Run `composer dump-autoload` to re-patch. |
| `Logo not found` | Invalid image path. | Use absolute paths via `public_path()` or `resource_path()`. |
| `Permission denied` | Script lacks execution bits. | Run `chmod +x setup-jasper.sh`. |

---

## 📜 License
MIT
