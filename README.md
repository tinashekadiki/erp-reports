# Jasper Reports for Laravel (NextERP)

A robust, enterprise-grade wrapper for Jasper Reports in Laravel, designed to seamlessly handle Java compatibility issues on modern systems (macOS Apple Silicon, Linux, etc.) by enforcing the use of Java 8.

## 🚀 Features

- **Pro-Level Integration**: Seamless wrapper around `phpjasper`.
- **Java 8 Compatibility Engine**: Solves the notorious `ClassCastException` found when running Jasper on Java 17+.
- **Automated Binary Patching**: Automatically patches the underlying `jasperstarter` executable to use a strictly defined local Java 8 binary.
- **Enterprise Reports**: Includes pre-built templates for Trial Balance, Income Statement, and Balance Sheet.

---

## 🛠 Prerequisites & Setup

### 1. The Java 8 Requirement
Jasper Reports (specifically the libraries used by `jasperstarter`) rely on legacy Java 8 class loaders. Running this on Java 17, 21, or newer will result in crashes like:
`java.lang.ClassCastException: class jdk.internal.loader.ClassLoaders$AppClassLoader cannot be cast to class java.net.URLClassLoader`

### 2. Installing Local Java 8 (Portable)
We do **not** rely on the system-wide Java version. Instead, we use a portable local installation.

**Run these commands in your project root:**

```bash
# 1. Create the local directory
mkdir -p .java8

# 2. Download and extract Zulu OpenJDK 8 (MacOS ARM64 Example)
# You may need to adjust the URL for Linux/Windows
curl -L https://cdn.azul.com/zulu/bin/zulu8.84.0.15-ca-jdk8.0.442-macosx_aarch64.tar.gz | tar -xz -C .java8 --strip-components=1

# 3. Verify it works
./.java8/bin/java -version
```

### 3. Automatic Patching
This package works in tandem with the root `post-update-cmd.php` script to ensure `jasperstarter` ALWAYS uses the local Java 8.

**How it works:**
1. Composer runs `post-autoload-dump`.
2. The script checks for `.java8/bin/java`.
3. It rewrites the shebang and execution line in `vendor/geekcom/phpjasper/bin/jasperstarter/bin/jasperstarter`.

If you ever encounter Java errors, simply run:
```bash
composer dump-autoload
```

---

## 📦 Installation

Add the package via Composer (configured as a local path repository):

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/nexterp/*"
    }
],
"require": {
    "nexterp/jasper-reports": "@dev"
}
```

---

## 💻 Usage

### Generating a Report

Inject the `JasperReportService` into your controller:

```php
use Nexterp\JasperReports\JasperReportService;

class ReportController extends Controller
{
    protected $jasper;

    public function __construct(JasperReportService $jasper)
    {
        $this->jasper = $jasper;
    }

    public function download()
    {
        $input = resource_path('reports/trial_balance.jrxml');
        $output = storage_path('app/reports/result_'.time());
        
        $params = [
            'company_name' => 'My Company',
            'financial_year' => '2025'
        ];

        // Generates PDF and returns the path
        $path = $this->jasper->generateReport($input, $output, ['pdf'], $params);
        
        return response()->file($path);
    }
}
```

### Compiling Templates (.jrxml to .jasper)

For better performance, compile templates only when they change.

```php
$this->jasper->compile(resource_path('reports/my_report.jrxml'));
```

---

## 📊 Included Reports

The package publishes standard templates to `resources/reports/vendor/jasper-reports`:

- `trial_balance.jrxml`: Consolidated debits/credits.
- `income_statement.jrxml`: P&L categorized by financial categories.
- `balance_sheet.jrxml`: Assets vs Liabilities.

## 🔧 Troubleshooting

| Error | Cause | Solution |
|-------|-------|----------|
| `ClassCastException` | System Java (17+) is being used. | Run `composer dump-autoload` to re-patch the binary. Check `.java8` exists. |
| `Parameter ... does not exist` | Passing params not defined in `.jrxml`. | Add `<parameter name="name" class="class"/>` to your JRXML file. |
| `Font not found` | Missing font extensions. | Use standard fonts (SansSerif) or install font extensions. |

---

## 📜 License

MIT
