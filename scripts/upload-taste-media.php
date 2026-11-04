<?php
// Uploads the taste-mockup photos and SVG patterns (zigzag edges, airmail stripes); writes content-source/taste-media.json.
if (untrailingslashit(home_url()) !== 'http://mpcbc.local') throw new Exception('wrong site');
require_once ABSPATH . 'wp-admin/includes/image.php';
$src = 'C:/Projects/mpcbc/taste-mockup/assets/';
$files = ['newsletter-2026-07.jpg','newsletter-2026-01.jpg','sanctuary-aisle.jpg','sanctuary-vbs.jpg','courtyard-lights.jpg','vbs-crafts.jpg','vbs-wave.jpg','vbs-floor.jpg','church-commonwealth.jpg','steeple-stamp.jpg','pastor-caleb.jpg',
  'svg/zigzag.svg','svg/zigzag-down.svg','svg/airmail-stripes.svg','svg/scallop.svg'];
$map = [];
foreach ($files as $f) {
  $name = 'taste-' . basename($f);
  $existing = get_posts(['post_type'=>'attachment','name'=>sanitize_title(pathinfo($name, PATHINFO_FILENAME)),'numberposts'=>1,'post_status'=>'inherit']);
  if ($existing) { $id = $existing[0]->ID; }
  else {
    $path = str_starts_with($f, 'svg/') ? 'C:/Projects/mpcbc/content-source/taste-' . $f : $src . $f;
    $up = wp_upload_dir(); $dest = $up['path'] . '/' . $name; copy($path, $dest);
    $type = str_ends_with($name, '.svg') ? 'image/svg+xml' : 'image/jpeg';
    $id = wp_insert_attachment(['post_mime_type'=>$type,'post_title'=>pathinfo($name, PATHINFO_FILENAME),'post_status'=>'inherit'], $dest);
    if ($type === 'image/jpeg') wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $dest));
  }
  $map[basename($f)] = ['id'=>$id, 'url'=>wp_get_attachment_url($id)];
  echo basename($f), " $id\n";
}
file_put_contents('C:/Projects/mpcbc/content-source/taste-media.json', wp_json_encode($map, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
