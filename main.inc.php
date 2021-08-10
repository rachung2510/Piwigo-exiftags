<?php

/*
Plugin Name: EXIF Tags
Version: 1.0
Description: Use ExifTool to convert IPTC keywords to Piwigo tags
Plugin URI: https://github.com/rachung2510/Piwigo-exiftags
Author: rachung
Author URI: https://github.com/rachung2510
*/

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

add_event_handler('loc_end_add_uploaded_file', 'convert_iptc_keywords');
function convert_iptc_keywords($image_infos) {
  // paths
  $filepath = $image_infos['path'];
  $perl = '/usr/bin/perl';
  $exif_path = PHPWG_PLUGINS_PATH.'tmp/exiftool.pl';

  // get keywords through ExifTool commandline
  $output = shell_exec($perl.' '.$exif_path.' -json "'.$filepath.'"');
  $metadata = json_decode($output, true);

  // add Piwigo tags
  $tag_id_arr = array();
  if (isset($metadata[0]['Keywords'])) {
    foreach ($metadata[0]['Keywords'] as $tag) {
      array_push($tag_id_arr,$tag);
    }
    $tag_ids = get_tag_ids($tag_id_arr);
    add_tags($tag_ids, array($image_infos['id']));
  }

  return $image_infos;
}
