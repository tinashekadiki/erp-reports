#!/bin/bash

# Exit on error
set -e

# Configuration
PASSWORD="nexterp123"
JAVA_VERSION="8.0.442"
ZULU_VERSION="8.84.0.15"
JASPERSTARTER_VERSION="0.7.4"
INSTALL_DIR="$(pwd)"
JAVA_DIR="$INSTALL_DIR/.java8"
LIB_DIR="$INSTALL_DIR/lib"
BIN_DIR="$INSTALL_DIR/bin"
TEMP_DIR="$INSTALL_DIR/tmp_setup"

# Password protection
echo -n "Enter password to run setup: "
read -s entered_password
echo

if [ "$entered_password" != "$PASSWORD" ]; then
    echo "❌ Incorrect password. Access denied."
    exit 1
fi

echo "✅ Password accepted. Starting setup..."

# Create directories
mkdir -p "$JAVA_DIR"
mkdir -p "$LIB_DIR"
mkdir -p "$BIN_DIR"
mkdir -p "$TEMP_DIR"

# Detect OS and Architecture
OS="$(uname -s)"
ARCH="$(uname -m)"

echo "🔍 Detected OS: $OS ($ARCH)"

# Java 8 Installation (Zulu OpenJDK)
if [ ! -f "$JAVA_DIR/bin/java" ] && [ ! -f "$JAVA_DIR/Contents/Home/bin/java" ]; then
    echo "📥 Downloading Zulu OpenJDK 8..."
    if [ "$OS" == "Darwin" ]; then
        if [ "$ARCH" == "arm64" ]; then
            JAVA_URL="https://cdn.azul.com/zulu/bin/zulu$ZULU_VERSION-ca-jdk8.0.442-macosx_aarch64.tar.gz"
        else
            JAVA_URL="https://cdn.azul.com/zulu/bin/zulu$ZULU_VERSION-ca-jdk8.0.442-macosx_x64.tar.gz"
        fi
    elif [ "$OS" == "Linux" ]; then
        if [ "$ARCH" == "x86_128" ] || [ "$ARCH" == "x86_64" ]; then
            JAVA_URL="https://cdn.azul.com/zulu/bin/zulu$ZULU_VERSION-ca-jdk8.0.442-linux_x64.tar.gz"
        else
            JAVA_URL="https://cdn.azul.com/zulu/bin/zulu$ZULU_VERSION-ca-jdk8.0.442-linux_aarch64.tar.gz"
        fi
    else
        echo "❌ Unsupported OS: $OS"
        exit 1
    fi

    curl -L "$JAVA_URL" | tar -xz -C "$JAVA_DIR" --strip-components=1
    echo "✅ Java 8 installed in $JAVA_DIR"
else
    echo "ℹ️ Java 8 is already installed."
fi

# JasperStarter Installation
if [ ! -f "$LIB_DIR/jasperstarter.jar" ]; then
    VENDOR_JAR="/Users/tinashekadiki/HIT/HIT/HIT/vendor/geekcom/phpjasper/bin/jasperstarter/lib/jasperstarter.jar"
    if [ -f "$VENDOR_JAR" ]; then
        echo "✅ Found existing JasperStarter JAR in vendor directory."
        VENDOR_LIB_DIR=$(dirname "$VENDOR_JAR")
        cp -r "$VENDOR_LIB_DIR/"* "$LIB_DIR/"
    else
        echo "📥 Downloading JasperStarter v$JASPERSTARTER_VERSION as fallback..."
        
        # Final set of URL patterns to try
        URLS=(
            "https://sourceforge.net/projects/jasperstarter/files/JasperStarter-0.7/$JASPERSTARTER_VERSION/jasperstarter-$JASPERSTARTER_VERSION-bin.zip/download"
            "https://downloads.sourceforge.net/project/jasperstarter/JasperStarter-0.7/$JASPERSTARTER_VERSION/jasperstarter-$JASPERSTARTER_VERSION-bin.zip"
            "https://downloads.sourceforge.net/project/jasperstarter/JasperStarter-0.7/JasperStarter-$JASPERSTARTER_VERSION-bin.zip"
        )

        SUCCESS=false
        for URL in "${URLS[@]}"; do
            echo "   Trying URL: $URL"
            if curl -L --fail "$URL" -o "$TEMP_DIR/jasperstarter.zip"; then
                if file "$TEMP_DIR/jasperstarter.zip" | grep -q "Zip archive data"; then
                    SUCCESS=true
                    break
                fi
            fi
            echo "   ⚠️ URL failed or returned invalid data. Trying next..."
        done

        if [ "$SUCCESS" = false ]; then
            echo "❌ Failed to download JasperStarter from all known URLs and not found in vendor."
            exit 1
        fi

        unzip -q "$TEMP_DIR/jasperstarter.zip" -d "$TEMP_DIR"
        
        # Locate the extracted directory
        EXTRACTED_DIR=$(find "$TEMP_DIR" -maxdepth 1 -type d -name "jasperstarter*" | head -n 1)
        
        if [ -n "$EXTRACTED_DIR" ]; then
            cp -r "$EXTRACTED_DIR/lib/"* "$LIB_DIR/"
            echo "✅ JasperStarter.jar and its libraries placed in $LIB_DIR"
        else
            echo "❌ Could not find extracted JasperStarter directory in $TEMP_DIR"
            exit 1
        fi
    fi
else
    echo "ℹ️ JasperStarter is already installed."
fi

# Patching bin/jasperstarter.stub
echo "🛠 Patching bin/jasperstarter.stub..."
cat > "$BIN_DIR/jasperstarter.stub" <<EOF
#!/bin/sh
## detect home folder
if(test -L "\$0") then
  auxlink=\`ls -l "\$0" | sed 's/^[^\>]*-\> //g'\`
  HOME_FOLDER=\`dirname "\$auxlink"\`/..
else
  HOME_FOLDER=\`dirname "\$0"\`/..
fi

# Use the local Java 8 installation
JAVA_CMD="\$HOME_FOLDER/.java8/bin/java"

# Fallback for MacOS structure if needed
if [ ! -f "\$JAVA_CMD" ]; then
    JAVA_CMD="\$HOME_FOLDER/.java8/Contents/Home/bin/java"
fi

if [ ! -f "\$JAVA_CMD" ]; then
    echo "❌ Java 8 not found in .java8. Please run setup-jasper.sh again."
    exit 1
fi

"\$JAVA_CMD" -jar "\$HOME_FOLDER/lib/jasperstarter.jar" "\$@"
EOF
chmod +x "$BIN_DIR/jasperstarter.stub"
echo "✅ bin/jasperstarter.stub patched."

# Compile all JRXML files
echo "🔨 Compiling all .jrxml files..."
JAVA_BINARY="$JAVA_DIR/bin/java"
[ ! -f "$JAVA_BINARY" ] && JAVA_BINARY="$JAVA_DIR/Contents/Home/bin/java"

# Find report files in resources/reports
REPORT_DIR="$INSTALL_DIR/resources/reports"
if [ -d "$REPORT_DIR" ]; then
    find "$REPORT_DIR" -name "*.jrxml" | while read -r jrxml_file; do
        echo "   Compiling: $(basename "$jrxml_file")"
        "$JAVA_BINARY" -jar "$LIB_DIR/jasperstarter.jar" compile "$jrxml_file"
    done
else
    echo "ℹ️ No report directory found at $REPORT_DIR. Skipping compilation."
fi

# Cleanup
rm -rf "$TEMP_DIR"

# Verify
echo "✨ Setup complete! Verifying..."
"$JAVA_BINARY" -version
if [ -f "$LIB_DIR/jasperstarter.jar" ]; then
    echo "✅ JasperStarter found at $LIB_DIR/jasperstarter.jar"
fi

echo "🚀 You can now run Jasper reports using 'bin/jasperstarter.stub'"
