<?php
$caPath = getenv('MYSQL_ATTR_SSL_CA');
if (!$caPath || !file_exists($caPath)) {
    fwrite(STDERR, "Error: MYSQL_ATTR_SSL_CA is not set or file does not exist.\n");
    exit(1);
}
$content = file_get_contents($caPath);
if (!str_contains($content, 'BEGIN CERTIFICATE')) {
    fwrite(STDERR, "Error: The CA certificate file does not appear to contain a valid PEM certificate.\n");
    exit(1);
}
echo "CA Certificate format verified successfully.\n";
exit(0);
