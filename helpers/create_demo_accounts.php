<?php

require_once(dirname(__FILE__) . '/../api/clipping.php');
require_once(dirname(__FILE__) . '/../api/file.php');
require_once(dirname(__FILE__) . '/../api/shared_clipping.php');
require_once(dirname(__FILE__) . '/../api/user.php');
require_once(dirname(__FILE__) . '/../api/comment.php');

// Create the users data.
// Demo account credentials.
$demo_users = [];

// Professor McMahan (demo user) credentials.
$demo_users['demo'] = new stdClass;
$demo_users['demo']->email = 'rm@example.com';
$demo_users['demo']->password = '123testing';
$demo_users['demo']->fname = 'Ryan';
$demo_users['demo']->lname = 'McMahan';

// Team member account credentials.
$demo_users['sb'] = new stdClass;
$demo_users['sb']->email = 'sb@example.com';
$demo_users['sb']->password = '123testing';
$demo_users['sb']->fname = 'Stephanie';
$demo_users['sb']->lname = 'Brisendine';

$demo_users['jd'] = new stdClass;
$demo_users['jd']->email = 'jd@example.com';
$demo_users['jd']->password = '123testing';
$demo_users['jd']->fname = 'Jonathan';
$demo_users['jd']->lname = 'Darling';

$demo_users['od'] = new stdClass;
$demo_users['od']->email = 'od@example.com';
$demo_users['od']->password = '123testing';
$demo_users['od']->fname = 'Omar';
$demo_users['od']->lname = 'Darwish';

$demo_users['je'] = new stdClass;
$demo_users['je']->email = 'je@example.com';
$demo_users['je']->password = '123testing';
$demo_users['je']->fname = 'Justin';
$demo_users['je']->lname = 'Ehlert';

$demo_users['tl'] = new stdClass;
$demo_users['tl']->email = 'tl@example.com';
$demo_users['tl']->password = '123testing';
$demo_users['tl']->fname = 'Twinkle';
$demo_users['tl']->lname = 'Lam';

// Pre-populate demo account data. //////////
// Create the user accounts.
foreach($demo_users as &$user) {
  $uid = userCreateUser($user->email,
    $user->password,
    $user->fname,
    $user->lname);
  $user->id = $uid;
  echo 'Account created for ' . $user->fname . ' ' . $user->lname . ' with uid ' . $uid . '.<br />';
}

// TODO: Give "demo" 2 notebooks.

// Give "demo" some clippings.
$demo_user_clippings = [];

$file = storeFile('demo_upload_biology.txt', 'txt', $demo_users['demo']->id);
$clipping = saveClipping(
  $demo_users['demo']->id,
  $file,
  'Biology is a natural science concerned with the study of life and living organisms, including their structure, function, growth, evolution, distribution, and taxonomy.',
  'Define: Biology',
  'The definition of Biology');
$demo_user_clippings[] = $clipping;
echo 'Clipping "Define: Biology" with cid ' . $clipping . ' created from file "demo_upload_biology.txt" with fid ' . $file . '.<br />';

$file = storeFile('demo_upload_php.txt', 'txt', $demo_users['demo']->id);
$clipping = saveClipping(
  $demo_users['demo']->id,
  $file,
  'PHP is a server-side scripting language designed for web development but also used as a general-purpose programming language.',
  'What is PHP?',
  'Overview of PHP');
$demo_user_clippings[] = $clipping;
echo 'Clipping "What is PHP?" with cid ' . $clipping . ' created from file "demo_upload_php.txt" with fid ' . $file . '.<br />';

$file = storeFile('demo_upload_nodejs.txt', 'txt', $demo_users['demo']->id);
$clipping = saveClipping(
  $demo_users['demo']->id,
  $file,
  'Node.js is an open source, cross-platform runtime environment for server-side and networking applications. Node.js applications are written in JavaScript, and can be run within the Node.js runtime on OS X, Microsoft Windows, Linux and FreeBSD.

Node.js provides an event-driven architecture and a non-blocking I/O API that optimizes an application\'s throughput and scalability. These technologies are commonly used for real-time applications.

Node.js uses the Google V8 JavaScript engine to execute code, and a large percentage of the basic modules are written in JavaScript. Node.js contains a built-in library to allow applications to act as a Web server without software such as Apache HTTP Server or IIS.',
  'What is NodeJS?',
  'Overview of NodeJS');
$demo_user_clippings[] = $clipping;
echo 'Clipping "What is NodeJS?" with cid ' . $clipping . ' created from file "demo_upload_nodejs.txt" with fid ' . $file . '.<br />';

// Share the clippings and comment on them.
$share_targets = array(
  $demo_users['sb'],
  $demo_users['jd'],
  $demo_users['od'],
  $demo_users['je'],
  $demo_users['tl'],
);

$comments = array(
  'Super rad! Thanks for sharing!',
  'Neato fresh! Thanks for the info brosef!',
  'Gnarly!',
  'I found this shallow and pedantic',
  '私はこの憎む',
  'SWAG',
);

$share_toggle = true;
foreach ($demo_user_clippings as $cid) {
  foreach ($share_targets as $user) {
    if ($share_toggle) {
      $uid = $user->id;
      $clipping = getClippingById($cid);
      $newCid = saveClipping($uid, $clipping->ORIGFILE, $clipping->CONTENT, $clipping->NAME, $clipping->SUBTITLE);
      setUserNotification($uid, 'A new clipping has been shared with you!');
      shareClipping($newCid, $cid, $uid);
      createComment($newCid, $uid, $comments[rand(0, sizeof($comments) - 1)]);
      echo 'Shared clipping "' . $clipping->NAME . ' with user ' . $user->fname . ' ' . $user->lname . '<br />';
    }
    $share_toggle = !$share_toggle;
  }
}

echo 'Demo accounts created!<br />';
$img = 'http://' . $_SERVER['HTTP_HOST'] . $_IWP_DIR_ . '/assets/images/party_hard.gif';
echo '<img src="' . $img . '" />';
