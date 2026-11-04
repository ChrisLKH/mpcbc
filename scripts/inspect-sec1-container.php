<?php
$data = json_decode(file_get_contents('C:/Users/chris/.gemini/antigravity/brain/9739df51-8c64-4e54-894b-ccf7703fff25/scratch/section1_before.json'), true);

echo "Outer section 52038130 styles:\n";
echo json_encode($data['styles'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

echo "\nInner container 56d8471a styles:\n";
echo json_encode($data['elements'][0]['styles'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
