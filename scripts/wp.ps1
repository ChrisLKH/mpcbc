param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

$php = "C:\Users\chris\AppData\Roaming\Local\lightning-services\php-8.2.29+0\bin\win64\php.exe"
$phpIni = "C:\Users\chris\AppData\Roaming\Local\run\FyApIsbHN\conf\php\php.ini"
$wpCli = "C:\Program Files (x86)\Local\resources\extraResources\bin\wp-cli\wp-cli.phar"
$wpPath = "C:\Users\chris\Local Sites\mpcbc\app\public"

& $php -d display_errors=0 -c $phpIni $wpCli @Args --path=$wpPath
