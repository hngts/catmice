<?php /// 'HARDCODER NGTS' ~ boilerplate.catmice-safe;
declare (strict_types = 1);
/*:Licence -> https://hardcoder.xyz/?mkp=licence~catmice
  ^: ~ See https://hardcoder.xyz/?mkp=catmice~man for more details.
*/

use \H\Api; /*:

    requires: PHPv>=8.2 w ctype & mb_string;
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

      private const STREAM_CONTEXT_TIMEOUT = 30;     /// int
        /// Timeout for lazy third-party servers

      private const CLI_SERVER_STREAM = false;       /// bool
        /// Prevents fopen and streaming_context over cli-sapi

  // --- END OF CONFIGURABLES ! ---

  private const OPT_ARGUMENT = [ /// _GET  =>  $this

        'sq'  =>  'squeeze',
        'c'   =>  'comments',
        'bc'  =>  'blockCmt',
        's'   =>  'sig',
        'ex'  =>  'expandExternals',
  ];

  private const HALT = 'Not a proper request.'; /// Common message.

  private ?int $hours = null;                   /// Constructor Param 1
  private ?bool $squeeze = null;                /// Constructor Param 2
  private ?bool $comments = null;               /// Constructor Param 3
  private ?bool $blockCmt = null;               /// Constructor Param 4
  private ?bool $sig = null;                    /// Constructor Param 5
  private ?bool $expandExternals = null;        /// Constructor Param 6
  private ?string $basedir = null;              /// Constructor Param 7

  private ?int $pack = null;                    /// if > 0 (hours), will write to disk
  private ?bool $template_call = null;          /// Becomes true if template call
  private ?string $target_id = null;            /// cat or mice
  private ?string $mimeLocation = null;         /// Path for .mime.types
  private mixed $packed = null;                 /// Holds json template and packed.gz path

  public function __construct (...$p) {
    /// Check
    (\count ($p) !== 7) and die (
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
    ] as $oP => $boolVal) {
      $this-> $oP = ((bool)$boolVal);
      unset ($oP, $boolVal);
    } // .. and go !
    $this-> hours = ((int)$p[0]); $this-> hook____ ($p[6], $Public);
    !\in_array (Observer-> doctype, \array_keys (self::DIRECTORY_MASK))
    and die (self::HALT) // <- sEcuritate.
    or // .. and main Request and Response - done !
      $this-> catmice_file_collector ($Public, $catmice);
  }

  private function hook____ (string $fBdir, ?string &$Public = null): void {
    /// According to the $Public value and passed on $fBdir
    /// the script will know whether it is symlinked or not.
    $Public = \dirname ($_SERVER['SCRIPT_FILENAME']);
    $this-> basedir = \basename (($Public !== $fBdir) ? $Public : $fBdir);
    // This is for the options override via _GET parameter
    foreach (self::OPT_ARGUMENT as $opt => $property) {
      if (isset ($_GET[$opt]) && \property_exists ($this, $property)) {
        $this-> $property = ((bool)\filter_input (INPUT_GET, $opt, FILTER_UNSAFE_RAW));
      } unset ($property, $opt);
    } // --
      // Since this script is a part of bigger project, but at the same time
      // works as a standalone script, replacement for composer and alike follows.
      // If `H`(ardcoder)\Api class isn't loaded correctly, we need compatibility mimicry.
      \defined ('HNG_ACTIVE_PLATFORM') and (
        !\defined ('Api')
          and \define ('Api', Api::do())
      )
      or
        $this-> hngts_dependancy();
    // Template path becomes ready
    $this-> mimeLocation = Api-> dspa ($Public, self::TRADEMARK);
  }

  private function catmice_file_collector (string $dir, ?string &$catmice = null): void {
    /// Main operation method
    $filteredID = $this-> match_exact_type ($id, $extension, $c_type);
    $this-> template_call = \str_starts_with ($filteredID, "\176\176");
    $at_charset = ($id === 'cat' && self::AT_CHARSET);
    $dL1 = Api-> dspa (\dirname ($dir), $this-> basedir);
    $dL2 = Api-> dspa ($dL1, self::DIRECTORY_MASK[Observer-> doctype]);
    // Automatically create missing directories (if any).
    $trademark = Api-> dspa ($dL2, self::TRADEMARK);
    !\is_dir ($trademark) and \mkdir ($trademark, 0775, true);

    if (!$this-> template_call) {
      $this-> target_id = $id;
      $catmice = $this-> catmice_transmute (
        $filteredID, $extension, $dL1, $dL2, $collect);
      $this-> cat_charset_and_sig ($at_charset, $catmice);
    }
    else {
      $_GET = [ $id => $filteredID ]; // overwrite _GET
      $this-> target_id = \mb_substr ($filteredID, 2);

      if (!$this-> fetch_object_template ($extension, $this-> target_id)) {
        echo self::HALT . ' JSON missing or invalid!'; return;
      }

      $json = $this-> packed['json'];
      $this-> packed = $this-> packed['gz'];

      foreach (['hours', 'pack', 'sig'] as $prop)
      $this-> $prop = $json-> $prop; unset ($prop);

      if (\is_file ($this-> packed)) {
        $stored = $this-> hours_multiply ($this-> pack);
        $since = (\filemtime ($this-> packed) + $stored);
        if (\time() < $since) {
          $catmice = \gzinflate (
            \file_get_contents ($this-> packed)
          ); $file_ready = true;
        }
        else {
          unlink ($this-> packed);
          $file_ready = false;
        }
      }
      else $file_ready = false;
      // Bool file_ready means: stored and NOT expired
      if ($file_ready !== true || !\file_exists ($this-> packed)) {
        $mut = (($id === 'mice') ? 'comments':'expandExternals');
        foreach ($json-> concat as $n => $std) {
          foreach ([$mut, 'blockCmt', 'squeeze']
            as $prop) $this-> $prop = $std-> $prop;
          unset ($prop); $catmice .= $this-> catmice_transmute
          ($std-> $id, $extension, $dL1, $dL2, $collect);
          unset ($std, $json-> concat[$n], $n);
        }
        unset ($json, $mut);
        $this-> cat_charset_and_sig ($at_charset, $catmice);
      }
    }

    $TestHours = $hours = (Observer-> filterget['hrs'] ?? $this-> hours);
    $hours = (($TestHours < 1 && self::ZERO_HOUR_SKEPTIC)
      ? self::ZERO_POINT_IMMUTABLE
      : ((int)$TestHours)
    );

    $this-> headers_and_out ($hours, $c_type, $catmice);
  }

  private function match_exact_type (?string &$id, ?string &$ext, ?string &$c_type): ?string {
    /*: Self-explanatory */ [$id, $ext, $c_type] = match (Observer-> doctype) {
      'style' => [ 'cat', 'css', 'text/css' ]
      , 'script' => [ 'mice', 'js', 'text/javascript' ]
      , default => [ 'x', 'txt', 'text/plain' ] // <- this never sets
    }; return Observer-> filterget[$id];
  }

  private function cat_charset_and_sig (bool $condition, string &$catmice): void {
    /// Prepends charset for css and appends block comment signature
    if ($condition) $this-> cat_charset ($catmice);
    if ($this-> sig) $catmice .= $this-> catmice_signature();
  }

  private function hours_multiply (int|float $hours): int {
    /*:*/ return ((int)(60 * 60 * $hours));
  }

  private function catmice_transmute (
    string $id, string $ext, string $dL1, string $dL2, ?array &$collect
  ): string { /// This is most expensive method. Does 90% of the job.
    $this-> collect_iterator ($id, $ext, $dL1, $dL2, $collect);
    $this-> build_iterated_string ($collect, $catmice);
    $this-> collection_mockery ($ext, $catmice);
    if ($ext === 'css' && $this-> expandExternals) {
      // From url (param) to base64,...
      $cfn = 'url'; $pattern = $cfn .'\((.*?)\)/imsu';
      $catmice = \preg_replace_callback ('/'. $pattern,
      fn($extern) => $this-> expand_externals ($extern, $cfn),
      $catmice);
    }

    return $catmice;
  }

  private function cat_charset (string &$catmice): void {
    /// Prepend utf-8 charset for styles
    $At = '@charset "UTF-8";';
    $catmice = "{$At} " . \str_replace (
    [\strtolower ($At), $At], '', $catmice);
    unset ($At); // <- mere habit.
  }

  private function collect_iterator (
    string $q, string $ext, string $dL1, string $dL2, ?array &$collect = null
  ): void { /// Main iterator and a bLYAatIful! piece of code
    $collect = [];
    $catmice = \explode ("\176", $q);
    foreach ($catmice as $n => $suspect) {
      // $suspect is quite unknown here ..
      $suspect = \str_replace ('|', DSP, $suspect);
      $first = \mb_substr ($suspect, 0, 1);
      [ $job, $multi, $target ] = match ($first) {
        default =>  [ 'literal', 'no', $dL2 . DSP ]
        , "\044" => [ 'concat', 'yes',  $dL2 . DSP . 'chain.' ]
        , "\045" => [ 'well', // ....
            ((\mb_strpos ($suspect, '.') !== false)
              ? 'no':'yes'), $dL2 . DSP . 'pot.'
        ], "\056" => [ 'root', 'no', $dL1 . DSP ],
        "\140" => [ 'trademark', 'no', $dL2 . DSP . self::TRADEMARK . DSP ]
      }; if ($job !== 'literal') $suspect = \mb_substr ($suspect, 1);
      if ($multi === 'yes'): $directory = "$target$suspect";
        if (!\is_dir ($directory)): $collect[] = '/* Bad `'
          . $job . '` for `' . \basename ($directory) . '` */';
        else: $slice = \array_slice (scandir ($directory), 2);
          \natsort ($slice); foreach ($slice as $count => $file):
            if (\str_ends_with ($file, ".{$ext}"))
            $collect[$count] = \file_get_contents ($directory. DSP .$file);
            unset ($count, $slice);
          endforeach; unset ($file);
        endif;
      elseif ($multi === 'no'):
        if ($job === 'well'):
          $suspect = \explode ('.', $suspect);
          $chop = \array_shift ($suspect);
          $target .= $chop; unset ($chop);
          $suspect = \implode ('.', $suspect);
          $target .= DSP . "$suspect.$ext";
        else: $target .= "$suspect.$ext"; endif;
        $collect[] = ((!\is_file ($target))
          ? '/* Bad request for `'
              . \basename ($target).'`. */'
          : \file_get_contents ($target)
        );
      endif;
      unset ($suspect, $n, $job, $multi, $target, $first);
    }
  }

  private function build_iterated_string (array &$collect, ?string &$catmice = null): void {
    /// From collected array values to single - FAT - string
    $catmice = ''; foreach ($collect as $n => $sample) {
      $catmice .= \trim ($sample) . EOL . EOL;
      unset ($collect[$n], $sample, $n);
    } $collect = null; unset ($collect);
  }

  private function collection_mockery (string $extension, string &$catmice): void {
    Squeezes content, removes block comments and|or line comments
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
  }

  private function expand_externals (array $extern, string $glue): string {
    /// Convert suitable images and fonts to base64 string
    $suspect = \trim ($extern[1]);
    if (\str_starts_with ($suspect, 'data:'))
      return $extern[0];
    $lC = \mb_substr ($suspect, -1);
    $fC = \mb_substr ($suspect, 0, 1);
    if ($fC === $lC && \in_array ($fC, [ '"', "'" ]))
     $suspect = \trim (\mb_substr ($suspect, 1, -1));
    $httpTest = \str_starts_with ($suspect, 'http');
    $slashTest = \str_starts_with ($suspect, '//');

    if (!$httpTest || !$slashTest) {
      $checkLocalAndQuery = true;
      $uTest = \parse_url ($suspect);
      $imp = ((\str_starts_with ($suspect, '/')) ? '' : '/');

      if (!isset ($uTest['query'])) {
        // url = ./../.. or /rel/to/file.ext
        $localTest = false; $real = \realpath ($suspect);
        $suspect = ((!\is_bool ($real) && $suspect !== $real)
        ? $real : "{$_SERVER['DOCUMENT_ROOT']}$imp{$suspect}");
        if (!\file_exists ($suspect)) return $extern[0];

        $extension = \pathinfo ($suspect, PATHINFO_EXTENSION);
        return $glue . $this-> expansion_summarum ($extern[0],
          $extension, \apacheMimeTypes ($extension,
          $this-> mimeLocation), $suspect, false
        );
      }
    }

    if (\count ($uTest) === 2 && (isset ($uTest['query'], $uTest['path']))) {
      if (isset ($checkLocalAndQuery)) unset ($checkLocalAndQuery);
      if (!self::CLI_SERVER_STREAM && PHP_SAPI === 'cli-server') return $extern[0];
      $httpHost = 'http'.((!isset ($_SERVER['HTTPS'])) ? '':'s')."://{$_SERVER['HTTP_HOST']}";
      $suspect = "{$httpHost}{$imp}" . \implode ('?', [ $uTest['path'], $uTest['query'] ]);
      $localTest = true; unset ($uTest);
    }

    // $httpTest = url(http(s)://..)
    // $slashTest = url(//www.website.com)
    // $localTest = url(/?someget=somevalue)

    if (!isset ($checkLocalAndQuery) && ($httpTest || $slashTest || $localTest)) {
      if ($slashTest) $suspect = \explode (':', $httpHost)[0] . ":{$suspect}";
      $context = \stream_context_create (['http' => ['ignore_errors' => true
        , 'timeout' => self::STREAM_CONTEXT_TIMEOUT
      ]]);

      if (!$fp = @\fopen ($suspect, 'r', false, $context))
      return $extern[0]; $meta = stream_get_meta_data ($fp);
      $this-> stream_wrapper_mimetype ($meta, $mime);
      \fclose ($fp); if ($mime === null) return $extern[0];
      return $glue . $this-> expansion_summarum ($extern[0],
        \explode ('/', $mime)[1], $mime, (@\file_get_contents
        ($suspect, false, $context) ?? false), true);
    }
  }

  private function stream_wrapper_mimetype (array &$meta, ?string &$fm = null): void {
    /// Extract Content-Type from stream_wrapper_data
    foreach ($meta['wrapper_data'] as $header) {
      $Bomb = \explode (':', $header);
      if (isset ($Bomb[1]) && Api-> flatten
        ($Bomb[0], 1) === 'content-type') {
        $fm = Api-> flatten ($Bomb[1], 1);
        break;
      }
      unset ($Bomb, $header);
    } $meta = null;
  }

  private function expansion_summarum (
    string $original, string $ext, bool|string $mime, bool|string $suspect, bool $stream
  ): string { /// Finally returns encoded target or gives back path as is
    if (\in_array ($ext, [ 'svg', 'svgz' ])) $mime = 'not_compliant';
    return ((!$suspect || !$mime || $mime === 'not_compliant')
      ? $original : "(data:$mime;charset=utf8;base64,"
      . \base64_encode ((($stream) ? $suspect
        : \file_get_contents ($suspect)
      )) . ')'
    );
  }

  private function catmice_signature(): string {
    /// Reveal or hide minor details about `self-decisions`.
    if (!$this-> template_call) {
      $doctype = Observer-> doctype;
      $against = Observer-> filterget[$this-> target_id];
    }
    else {
      $against = $this-> target_id; $dynamic = 'Complicated!';
      $doctype = 'Template ' . \strtoupper (Observer-> doctype);
    }

    return EOL . EOL
      . '/**: ' . "Hardcoder::catmice `$doctype` against: \"" . $against ."\""
        . EOL . EOL . "\t- Squeezed: " . \var_export (($dynamic ?? $this-> squeeze), true). EOL
          . ((Observer-> doctype === 'style')
            ? "\t- Expand Externals: " . \var_export (($dynamic ?? $this-> expandExternals), true) . EOL
            : "\t- Comments persist: " . \var_export (($dynamic ?? $this-> comments), true) . EOL
        ) . "\t- Block-Comments persist: " . \var_export (($dynamic ?? $this-> blockCmt), true)
        . EOL . EOL
    . ':*/';
  }

  private function fetch_object_template (string $ext, string $tpl): bool {
    /// Prepare template workspace and parse JSON template into string
    $target = $this-> make_template_target ('json', $ext, $tpl);
    $tpldir = \dirname ($target); !\is_dir ($tpldir) and \mkdir ($tpldir, 0775, true);
    $wall = $tpldir . DSP . 'index.php'; !\is_file ($wall) and \touch ($wall);
    if (!\is_file ($target) || !\ctype_alpha ($tpl)) return false;
    $this-> packed = [ // ^^ Simple test/ portion abovereturn
      'gz' => $this-> make_template_target ('packed.gz', $ext, $tpl),
      'json' => (@\json_decode (\file_get_contents ($target), false, JSON_UNESCAPED_UNICODE))
    ]; return \is_object ($this-> packed['json']);
  }

  private function make_template_target (string $which, string $ext, string $tpl): string {
    /// Returns either json template path or path for packed.gz outcome.
    return Api-> dspa ($this-> mimeLocation, '.tpl', "{$ext}.{$tpl}.{$which}");
  }

  private function template_call_pack (string &$catmice): void {
    /// Write template collection to disk and release catmice
    echo $catmice; ($this-> template_call && $this-> pack > 0)
      and \file_put_contents ($this-> packed,
        \gzdeflate ($catmice, 9), LOCK_EX);
    $catmice = null;
  }

  private function headers_and_out (int|float $hours, string $c_type, string &$catmice): never {
    /// Send response headers, spit collection and optionally gzcompress template to disk
    $seconds = $this-> hours_multiply ($hours);
    $time = \time(); $gdstring = 'D, d M Y H:i:s';
    $expires = \gmdate ($gdstring, ($time + $seconds));
    $lastMod = \gmdate ($gdstring, ((int)($time - ($time / 20))));
    foreach (\array_merge (['Timing-Allow-Origin' => '*', 'Content-Allow-Origin' => '*'
        , 'Cache-Control' => (($hours < self::ZERO_POINT_IMMUTABLE) ? 'no-cache'
        : "max-age={$seconds}, immutable"), 'Last-Modified' => "$lastMod GMT",
        'Expires' => "$expires GMT", 'X-Content-Type-Options' => "nosniff",
        'Content-Encoding' => 'gzip, deflate, br'], (($this-> target_id === 'cat')
      ? [] : [ 'Content-Security-Policy' => "default-src 'none'",
        'X-Content-Security-Policy' => "default-src 'none'"
      ]), [ 'Content-Type' => "$c_type; charset=utf-8" ]
    ) as $key => $value): \header ("{$key}: {$value}");
      unset ($value, $key);
    endforeach;
    \ob_start();
      \ob_start ('ob_gzhandler');
        \header ('Transfer-Encoding: gzip, deflate, br');
        $this-> template_call_pack ($catmice);
      ob_end_flush();
      \header ('Content-Length: '
        . \ob_get_length());
      \ob_end_flush();
    exit;
  }

  private function hngts_dependancy(): void {
    /// Generates missing and vital data from hngts toolset

    function apacheMimeTypes (string $extension = '', string $basedir = ''): string|false {
      /*: This function is a substitue for native 'mime_content_type';
        $pathinfo = pathinfo ($_SERVER['SCRIPT_FILENAME'];
        $mime_content_type = array_merge ($pathinfo, [
          'mimetype' =>  apacheMimeTypes (($pathinfo['extension'] ?? 'txt'), __DIR__)
        ]);
      */

      $apache = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf';
      $basename = 'mime.types'; $serialized = "$basedir/$basename";
      $ext = (($extension === '') ? 'txt' : $extension);
      if (\is_file ($serialized)) {
        //
        $MimeType = \unserialize (\file_get_contents ($serialized));
        // For hntgs-yards, mimetype should always give false.
        if (\is_array ($MimeType)) return $MimeType[$ext] ?? false;

      }
      else {
        //! BlyAfiful block of code. :)
        $MimeType = \file ("$apache/$basename", 1|2|4);
        \is_array ($MimeType) or die (__FUNCTION__ . ' says: '
        . "Raw '$basename' whitelist file creation failed.");
        $Mtype = []; foreach ($MimeType as $n => $line) {
        if (\mb_substr ($line, 0, 1) !== '#') {
          $line = \preg_replace ('/\s+/', ' ', $MimeType[$n]);
          $Bomb = \explode (' ', $line);
          $Mime = \array_shift ($Bomb);
          $Bomb = \array_reverse ($Bomb);
          foreach ($Bomb as $e) {
            if ($e !== '' && !isset ($MimeType[$e]))
              $Mtype[$e] = $Mime; unset ($e);
          } unset ($Bomb, $Mime);
        } unset ($line, $MimeType[$n], $n);
        } unset ($MimeType);
        $Mtype = \array_filter ($Mtype);
        \is_dir ($basedir) or \mkdir ($basedir, 0755, true);
        \file_put_contents ($serialized, \serialize ($Mtype), LOCK_EX);
        return $Mtype[$ext] ?? false;
      }
    }

    function globPublicTD (array $list):void {
      /// Define `global Public Temporal Data`.
      foreach ($list as $name => $value) {
        !\defined ($name) and \define ($name, $value);
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

          $TestMatch = (@\implode (DSP, $suspects));

          !is_bool ($TestMatch)
            or die (__METHOD__ . ' args - invalid.'
            . EOL . EOL . \print_r ($suspects, true));
            unset ($suspects);

          $t = "\176";
          $TestMatch = ltrim ($TestMatch);

          if (mb_substr ($TestMatch, 0, 1) === $t)
            $out = $this-> tildenv ($TestMatch, $t);
          return \trim ($this-> no_slash (($out ?? $TestMatch)));
        }

        public function tildenv (string $path, string $char = "\176"): string {
          /*: Expands ~ to $HOME. */ return \str_replace ($char, \getenv ('HOME'), $path);
        }

        public function no_slash (string $a = ''): string {
          /// Directories must not have `/` or `\` at the end
          return ((\mb_substr ($a, -1) !== DSP) ? $a : \mb_substr ($a, 0, -1));
        }

        public function no_comments (string $x = "\057\057", string $a = '', array $merge = []):string {
          /// Remove '$x+? ' line comments from strings

          $a = \explode (EOL, $a);
          if (\count ($a) >= 1) {

            $diff = null;
            $merged = \array_merge (["\176", "\134", ' '], $merge);
            foreach ($a as $int => $b) {
              foreach ($merged as $sample) {
                $p = \mb_strpos ($b, "$x$sample");
                if ($p !== false) {
                  $a[$int] = \mb_substr ($b, 0, $p);
                  $diff = $int;
                  break;
                } unset ($p, $sample);
              }

              if (\is_int ($diff) && \trim ($a[$diff]) === '') {
                unset ($a[$diff]); $diff = null;
              } unset ($b, $int);
            }
          }

          return \implode (EOL, $a);
        }

        public function no_block_comments (string $a, string $r = ''): string {
          /// Remove C-style block comments from strings
          return \preg_replace ('!/\*.*?\*/!s', $r, $a);
        }

        public function flatten (string $suspect, int $mb = 0): string {
          /*: Trim and (mb_)strtolower suspect.
              Any other $mb than 1 will not be multibyte.
          */ return \trim (((($mb === 1) ? '\\mb_' : '')
          . 'strtolower') ($suspect));
        }

        public function one_line (string $a, bool $too_ugly = false): string {
          /// Entire string to one line - ugly or uglier.
          return \preg_replace ('/\s+/',
            ((!$too_ugly) ? ' ' : ''),
            (($too_ugly) ? $a : \trim ($a))
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
              $this-> filterget[$k] = \trim (\filter_input (INPUT_GET, $k, FILTER_UNSAFE_RAW));
              $this-> doctype = $description; unset ($description, $k);
              break;
            }
          }

          if (isset ($_GET['hrs']) && !isset ($this-> filterget['hrs'])) {
            $this-> filterget['hrs'] = ((int)\filter_input (INPUT_GET, 'hrs', FILTER_UNSAFE_RAW));
          }
        }
      }
    ]);
  }

};
