<?php
$cmd = 'xvfb-run -a env HOME=/tmp QTWEBENGINE_CHROMIUM_FLAGS="--user-data-dir=/tmp/qtwebengine" cutycapt --url=file:///tmp/test-ar.html --out=/tmp/test-web.png --min-width=576 2>&1';
exec($cmd, $out, $ret);
echo json_encode(['ret' => $ret, 'out' => $out]);
