<?php /// 'HARDCODER NGTS' ~ boilerplate.catmice-safe;
declare (strict_types = 1);
/*:Licence -> https://hardcoder.xyz/?mkp=licence~catmice
  ^: ~ See https://hardcoder.xyz/?mkp=catmice~man for more details.
*/

use \H\Api; /*:

    requires: PHPv>=8.2 w mb_string;
    (optional: hntgs);

*/
new class (0, false, true, true, true, false, __DIR__) {
  /*:-> get --- STATIC CONFIGURABLE PORTION ---

    1st:hrs     -     int               'hours_for_cache'
    2nd:sq      -     bool              'source_squeeze'
    3rd:c       -     bool              'comments_single'
    4th:bc      -     bool              'block_comments'
    5th:s       -     bool              'signature_print'
    6th:ex      -     bool              'expand_externals'
    7th:!!      -     string            'directory'

  */

      private const TRADEMARK = "\342\204\242";     /// string
        /// ™ special directory name


      private const DIRECTORY_MASK = [ /// Confront to other directory names

        'script' => 'script',
        //~ 'script' => '.mices', // ? . in front is public, but doesn't need to be.

        'style' =>  'style',
        //~ 'style' =>  '.cats',  // ? . in front means - system directory

      ];

    //~---

      private const AT_CHARSET = true;              /// bool
        /// Cats only.


      private const ZERO_HOUR_SKEPTIC = false;       /// bool
        /// Prevents TOTAL fresh content


      private const ZERO_POINT_IMMUTABLE = 0.0020;  /// float
        /// Works only if $hours === 0 and ZERO_HOUR_SKEPTIC === true



  // --- END OF CONFIGURABLES ! ---


  private const OPT_ARGUMENT = [ /// _GET  =>  $this

        'sq'  =>  'squeeze',
        'c'   =>  'comments',
        'bc'  =>  'blockCmt',
        's'   =>  'sig',
        'ex'  =>  'expandExternals',
  ];

  private ?int $hours = null;                 /// Constructor Param 1
  private ?bool $squeeze = null;              /// Constructor Param 2
  private ?bool $comments = null;             /// Constructor Param 3
  private ?bool $blockCmt = null;             /// Constructor Param 4
  private ?bool $sig = null;                  /// Constructor Param 5
  private ?bool $expandExternals = null;      /// Constructor Param 6
  private ?string $basedir = null;            /// Constructor Param 7

  public function __construct (...$p) {
    /// Check
    (count ($p) !== 7) and die (
      'Catmice' . __FUNCTION__
      . 'or error: args required 7; less found.'
      . PHP_EOL . 'Don\'t mess too much with parameters.'
    );

    // set ..
    foreach ([
      'sig' => $p[4],
      'squeeze' => $p[1],
      'comments' => $p[2],
      'blockCmt' => $p[3],
      'expandExternals' => $p[5],
    ] as $oP => $BoolVal) {
      $this-> $oP = ((bool)$BoolVal);
      unset ($oP, $BoolVal);
    } $this-> hours = ((int)$p[0]);
    // .. and go.
    $this-> hook____ ($p[6], $Public);
    !in_array (Observer-> doctype, array_keys (self::DIRECTORY_MASK))
    and die ('Not a proper request.') // <- sEcuritate.
    or // .. and main Request and Response - done !
      $this-> catmice_file_collector ($Public);
  }

  private function hook____ (string $F_bdir, &$Public) {
    ///
    // According to the $Public value and passed on $F_bdir
    // the script will know whether it is symlinked or not.

    $Public = dirname ($_SERVER['SCRIPT_FILENAME']);
    $this-> basedir = basename (($Public !== $F_bdir) ? $Public : $F_bdir);

    // This is for the options override via _GET parameter
    foreach (self::OPT_ARGUMENT as $opt => $property) {
      if (isset ($_GET[$opt]) && property_exists ($this, $property)) {
        $this-> $property = ((bool)filter_input (INPUT_GET, $opt, FILTER_UNSAFE_RAW));
      } unset ($property, $opt);
    }

    // --
    // Since this script is a part of bigger project, but at the same time
    // works as a standalone script, replacement for composer and alike follows.
    // If `H`(ardcoder)\Api class isn't loaded correctly, we need compatibility mimicry.
      defined ('HNG_ACTIVE_PLATFORM') and (
        !defined ('Api')
          and define ('Api', Api::do())
      )
      or
        $this-> hngts_dependancy();

  }

  private function catmice_file_collector (string $dir) {
    /// Main operation method

    $dirlevel1 = Api-> dspa (dirname ($dir), $this-> basedir);
    [$id, $extension, $content_type] = match (Observer-> doctype) {
      default => [ null, null, null ]
      , 'style' => [ 'cat', 'css', 'text/css' ]
      , 'script' => [ 'mice', 'js', 'text/javascript' ]
    }; $content_type = 'Content-Type: '
      . $content_type . ';charset=utf-8';

    $dirlevel2 = Api-> dspa ($dirlevel1,
    self::DIRECTORY_MASK[Observer-> doctype]);

    $TestHours = $hours = (Observer-> filterget['hrs'] ?? $this-> hours);
    $hours = (($TestHours < 1 && self::ZERO_HOUR_SKEPTIC)
      ? self::ZERO_POINT_IMMUTABLE : ((int)$TestHours)
    );

    // Automatically create missing directories (if any).
    $trademark = Api-> dspa ($dirlevel2, self::TRADEMARK);
    !is_dir ($trademark) and mkdir ($trademark, 0775, true);

    $collect = [];
    $catmice = explode ('~', Observer-> filterget[$id]);

    foreach ($catmice as $n => $suspect) {
      // $suspect is quite unknown here ..

      $suspect = str_replace ('|', DSP, $suspect);
      $first = mb_substr ($suspect, 0, 1);
      [ $job, $multi, $target ] = match ($first) {
        default =>  [ 'literal', 'no', $dirlevel2 . DSP ]
        , "\044" => [ 'concat', 'yes',  $dirlevel2 . DSP . 'chain.' ]
        , "\045" => [ 'well', ((mb_strpos ($suspect, '.') !== false) ? 'no':'yes'), $dirlevel2 . DSP . 'pot.' ]
        , "\056" => [ 'root', 'no', $dirlevel1 . DSP ]
        , "\140" => [ 'trademark', 'no', $dirlevel2 . DSP . self::TRADEMARK . DSP ]
      };

      if ($job !== 'literal') $suspect = mb_substr ($suspect, 1);
      if ($multi === 'yes')
      {
        $directory = "$target$suspect";
        if (!is_dir ($directory)) {
          $collect[] = '/* Bad `' . $job . '` for `'
            . basename ($directory) . '` */';
        }
        else
        {
          $slice = array_slice (scandir ($directory), 2);
            natsort ($slice);

          foreach ($slice as $count => $file)
            if (str_ends_with ($file, ".{$extension}"))
            $collect[$count] = file_get_contents ($directory. DSP .$file);

          unset ($file, $count, $slice);
        }
      }
      else
      if ($multi === 'no') {

        if ($job === 'well') {
          $suspect = explode ('.', $suspect);
          $chop = array_shift ($suspect);
          $target .= $chop;
          unset ($chop);
          $suspect = implode ('.', $suspect);
          $target .= DSP . "$suspect.$extension";
        }
        else {
          $target .= "$suspect.$extension";
        }

        if (!is_file ($target)) {
          $collect[] = '/* Bad request for `'
            . basename ($target).'`. */';
        }
        else {
          $collect[] = file_get_contents ($target);
        }

      }

      unset (
        $suspect
        , $n
        , $job
        , $multi
        , $target
        , $first
      );

    }

    $catmice = '';
    foreach ($collect as $n => $collected) {
      $catmice .= trim ($collected) . EOL . EOL;
      unset ($collect[$n], $collected, $n);
    } unset ($collect);

    if ($extension === 'css' && self::AT_CHARSET) {
      $At = '@charset "UTF-8";'; $catmice = $At
        . EOL . str_replace ($At, '', $catmice);
      unset ($At);
    }

    if ($this-> squeeze === true) {

      $this-> blockCmt = false;
      $this-> comments = false;

      $catmice = Api-> no_block_comments ($catmice);

      if ($extension === 'js')
        $catmice = Api-> no_comments (a: $catmice);

      $catmice = Api-> one_line ($catmice);
    }
    else {

      if ($this-> blockCmt !== true)
        $catmice = Api-> no_block_comments ($catmice);

      if ($extension === 'js' && !$this-> comments)
      $catmice = Api-> no_comments (a: $catmice);
    }

    if ($this-> expandExternals && $extension === 'css') {
      // From url (param) to url (..base64,989lkJFklskdfjlgjer ...) if plausable.
      $MimeLocation = Api-> dspa ($dir, self::TRADEMARK);
      $catmice = preg_replace_callback ('/\((.*?)\)/',
      function ($extern) use ($MimeLocation) {

        $suspect = trim ($extern[1]);
        $first_character = $suspect[0];
        $last_character = mb_substr ($suspect, -1);

        if ($first_character === $last_character
          && in_array ($first_character, [ '"', "'" ]))
            $suspect = trim (mb_substr ($suspect, 1, -1));

        if ($suspect[0] !== '/') $suspect = "/$suspect";

        $BoolTest = substr (str_replace
          ('.php', '', $suspect), 1);

        $suspect = ((str_replace //~ The following is for "Hngts RGB and media handlers"
          ([ 'rgb?',     //~ Rgb is for (strict) images and/or pictures  ..
            'media?',   //~ Media is all others - except those in cat, mice or xtx ..
          ], "", $BoolTest) !== $BoolTest) ? true
          : @realpath ("{$_SERVER['DOCUMENT_ROOT']}$suspect")
        );

        if (!is_string ($suspect) && is_bool ($suspect)) {
          if (!$suspect) return $extern[0];
          else {

            $Bomb = explode ('?', $BoolTest);
            $Dirty = mb_strpos ($Bomb[1], '&');

            if ($Dirty !== false)
              $Bomb[1] = mb_substr ($Bomb[1], 0, $Dirty);

            $suspect = explode ('=', $Bomb[1]);
            $Bomb[1] = $suspect;

            $suspect = Api-> dspa ($_SERVER['DOCUMENT_ROOT'],
              $Bomb[0], str_replace ('|', DSP, $Bomb[1][1]).".{$Bomb[1][0]}");

            unset ($Dirty, $Bomb);
          }

        }

        if (!file_exists ($suspect))
          return $extern[0]; // Final breakpoint

        $extension = pathinfo ($suspect, PATHINFO_EXTENSION);
        $file_mime = apacheMimeTypes ($extension, $MimeLocation);
        $file_mime = match ($extension) {
          default => 'txt'
          , 'otf' => 'font/opentype'
          , 'woff' => 'application/x-font-woff'
          , 'woff2' => 'application/x-font-woff2'
          , 'eot' => 'application/vnd.ms-fontobject'
          , 'ttf', 'ttc' => 'application/x-font-ttf'
          , in_array ($extension, [
            'svg', 'svgz'
            ]) => 'not_compliant'
        };

        return
          ((!$file_mime || $file_mime === 'not_compliant')
          ? $extern[0] : "(data:$file_mime;charset=utf8;base64,"
          . base64_encode (file_get_contents ($suspect)) . ')');

      }, $catmice);
    }

    $this-> headers_and_out ($hours, $content_type, $id, $catmice);
  }

  private function catmice_signature (string $id) {
    /// Reveal or hide minor details about `self-decisions`.

    $doctype = Observer-> doctype; return EOL . EOL
      . '/**: ' . "Hardcoder::catmice `$doctype` against: \"" . Observer-> filterget[$id] ."\""
        . EOL . EOL . "\t- Squeezed: " . var_export ($this-> squeeze, true). EOL . (($doctype === 'style')
          ? "\t- Expand Externals: " . var_export ($this-> expandExternals, true) . EOL
          : "\t- Comments persist: " . var_export ($this-> comments, true) . EOL
        ) . "\t- Block-Comments persist: " . var_export ($this-> blockCmt, true)
        . EOL . EOL
    . ':*/';
  }

  private function headers_and_out (int|float $hours, string $content_type, string $id, string $catmice) {
    ///

    $t = time();
    $gdstring = 'D, d M Y H:i:s';
    $seconds = ((int)(60 * (60 * $hours)));
    $expires = gmdate ($gdstring, ($t + $seconds));
    $lastMod = gmdate ($gdstring, ((int)($t - ($t / 20))));
    if ($this-> sig) $catmice .= $this-> catmice_signature ($id);
    //~
    foreach ([
      'Pragma' => "public"
      , 'Timing-Allow-Origin' => "*"
      , 'Content-Allow-Origin' => "*"
      , 'Content-Security-Policy' => "child-src 'self';"
      , 'Cache-Control' => "public, "
        . ((is_float ($hours))  ? 'immutable' : 'must-revalidate')
        . ", max-age={$seconds}", 'Last-Modified' => "$lastMod GMT"
      , 'Expires' => "$expires GMT"
      , 'X-Content-Type-Options' => "nosniff"
      , 'X-powered-By' => "hardcoder-catmice"
    ] as $key => $value) {
      header ("{$key}: {$value}");
      unset ($value, $key);
    }

    header ($content_type);
    ob_start();
      ob_start ('ob_gzhandler');
        header ("Transfer-Encoding: gzip");
        echo trim ($catmice); unset (
          $catmice, $lastMod, $expires,
          $seconds, $gdstring);
      ob_end_flush();
      header ('Content-Length: '
        . ob_get_length());
    ob_end_flush();
    exit;
  }

  private function hngts_dependancy() {
    /// Generates missing and vital data from hngts toolset

    function apacheMimeTypes (string $extension = '', string $basedir = '') {
      /*: This function is a substitue for native 'mime_content_type';
        $pathinfo = pathinfo ($_SERVER['SCRIPT_FILENAME'];
        $mime_content_type = array_merge ($pathinfo, [
          'mimetype' =>  apacheMimeTypes (($pathinfo['extension'] ?? 'txt'), __DIR__)
        ]);
      */

      $apache = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf';
      $basename = 'mime.types'; $serialized = "$basedir/$basename";
      $ext = (($extension === '') ? 'txt' : $extension);
      if (is_file ($serialized)) {
        //
        $MimeType = unserialize (@file_get_contents ($serialized));
        // For hntgs-yards, mimetype should always give false.
        if (is_array ($MimeType)) return $MimeType[$ext] ?? false;

      }
      else {
        //! BlyAfiful block of code. :)
        $MimeType = file ("$apache/mime.types", 1|2|4);
        is_array ($MimeType) or die (__FUNCTION__ . ' says: '
        . "Raw '$basename' whitelist file creation failed.");
        $Mtype = []; foreach ($MimeType as $n => $line) {
        if (mb_substr ($line, 0, 1) !== '#') {
          $line = preg_replace ('/\s+/', ' ', $MimeType[$n]);
          $Bomb = explode (' ', $line);
          $Mime = array_shift ($Bomb);
          $Bomb = array_reverse ($Bomb);
          foreach ($Bomb as $e) {
            if ($e !== '' && !isset ($MimeType[$e]))
              $Mtype[$e] = $Mime; unset ($e);
          } unset ($Bomb, $Mime);
        } unset ($line, $MimeType[$n], $n);
        } unset ($MimeType);
        $Mtype = array_filter ($Mtype);
        is_dir ($basedir) or mkdir ($basedir, 0755, true);
        file_put_contents ($serialized, serialize ($Mtype), LOCK_EX);
        return $Mtype[$ext] ?? false;
      }
    }

    function globPublicTD (array $list):void {
      /// Define `global Public Temporal Data`.
      foreach ($list as $name => $value) {
        !defined ($name) and define ($name, $value);
        unset ($value, $name);
      }
    }

    globPublicTD ([
      'NSP' => "\134"                       // Escape \ as Namespace separator.
      , 'EOL' => PHP_EOL                    // goto: https://en.wikipedia.org/wiki/Newline;
      , 'PSP' => PATH_SEPARATOR             // On unix-alike boxes it is : on m$ boxes it is ;
      , 'DSP' => DIRECTORY_SEPARATOR        // On unix-alike boxes it is / on m$ boxes it is \
      , 'Api' => new class {                /// Shortened version of Public \H\Api as public constant fakery

        public function dspa (...$suspects):string {
          /// From args to: / + arg1/arg2/arg3 ..

          $TestMatch = (@implode (DSP, $suspects));

          !is_bool ($TestMatch)
            or die (__METHOD__ . ' args - invalid.'
            . EOL . EOL . print_r ($suspects, true));
            unset ($suspects);

          $t = "\176";
          $TestMatch = ltrim ($TestMatch);

          if (mb_substr ($TestMatch, 0, 1) === $t)
            $out = $this-> tildenv ($TestMatch, $t);
          return trim ($this-> no_slash (($out ?? $TestMatch)));
        }

        public function tildenv (string $path, string $char = "\176"): string {
          /*: Expands ~ to $HOME. */ return str_replace ($char, getenv ('HOME'), $path);
        }

        public function no_slash (string $a = ''): string {
          /// Directories must not have `/` or `\` at the end
          return ((mb_substr ($a, -1) !== DSP) ? $a : mb_substr ($a, 0, -1));
        }

        public function no_comments (string $x = "\057\057", string $a = '', array $merge = []):string {
          /// Remove '$x+? ' line comments from strings

          $a = explode (EOL, $a); if (count ($a) >= 1) {
            $merged = \array_merge ([NSP, "\176", ' ' ], $merge);
            foreach ($a as $int => $b)
            { foreach ($merged as $sample)
            { $p = mb_strpos ($b, "$x$sample");
              if ($p !== false)
                $a[$int] = mb_substr ($b, 0, $p);
              unset ($p, $sample);
            } unset ($b, $int); }
          } return implode (EOL, $a);
        }

        public function no_block_comments (string $a):string {
          /// Remove C-style block comments from strings
          return preg_replace ('!/\*.*?\*/!s', EOL, $a);
        }

        public function one_line (string $a, bool $too_ugly = false) {
          /// Entire string to one line - ugly or uglier.
          return preg_replace ('/\s+/',
            ((!$too_ugly) ? ' ' : ''),
            (($too_ugly) ? $a : trim ($a))
          );
        }

      }
      , 'Observer' => new class {           /// Shortened version of Temporal Observer stream

        public $doctype;  ///
        public $filterget = []; ///

        public function __construct () {
          /// This is where cat and mice keywords are determined.
          foreach (['cat' => 'style', 'mice' => 'script' ] as $k => $description) {
            if (isset ($_GET[$k]) && !isset ($this-> filterget[$k])) {
              $this-> filterget[$k] = trim (filter_input (INPUT_GET, $k, FILTER_UNSAFE_RAW));
              $this-> doctype = $description; unset ($description, $k);
              break;
            }
          }

          if (isset ($_GET['hrs']) && !isset ($this-> filterget['hrs'])) {
            $this-> filterget['hrs'] = ((int)filter_input (INPUT_GET, 'hrs', FILTER_UNSAFE_RAW));
          }
        }
      }
    ]);
  }

};
