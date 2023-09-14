<?php
  file_put_contents('temp/test.text', '123');
  rename('temp/test.text', 'uploads/123');
?>