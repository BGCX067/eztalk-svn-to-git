<?php
if(!defined('IN_ET')) {
	exit('Access Denied');
}

require_once ('template.func.php');

class Template
{

    const DIR_SEP = DIRECTORY_SEPARATOR;

    /**
     * ģ��ʵ��
     *
     * @staticvar
     * @var object Template
     */
    protected static $_instance;

    /**
     * ģ�������Ϣ
     *
     * @var array
     */
    protected $_options = array();

    /**
     * ����ģʽ���÷���
     *
     * @static
     * @return object Template
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof self)
            self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * ���췽��
     *
     * @return void
     */
    private function __construct()
    {
        $this->_options = array(
            'template_dir' => 'templates' . self::DIR_SEP, //ģ���ļ�����Ŀ¼
            'cache_dir' => 'templates' . self::DIR_SEP . 'cache' . self::DIR_SEP, //�����ļ����Ŀ¼
            'auto_update' => false, //��ģ���ļ��Ķ�ʱ�Ƿ��������ɻ���
            'cache_lifetime' => 0, //������������(����)��Ϊ 0 ��ʾ����
        );
    }

    /**
     * �趨ģ�������Ϣ
     *
     * @param  array $options ��������
     * @return void
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value)
            $this->set($name, $value);
    }

    /**
     * �趨ģ�����
     *
     * @param  string $name  ��������
     * @param  mixed  $value ����ֵ
     * @return void
     */
    public function set($name, $value)
    {
        switch ($name) {
            case 'template_dir':
                $value = $this->_trimpath($value);
                if (!file_exists($value))
                    $this->_throwException("δ�ҵ�ָ����ģ��Ŀ¼ \"$value\"");
                $this->_options['template_dir'] = $value;
                break;
            case 'cache_dir':
                $value = $this->_trimpath($value);
                if (!file_exists($value))
                    $this->_throwException("δ�ҵ�ָ���Ļ���Ŀ¼ \"$value\"");
                $this->_options['cache_dir'] = $value;
                break;
            case 'auto_update':
                $this->_options['auto_update'] = (boolean) $value;
                break;
            case 'cache_lifetime':
                $this->_options['cache_lifetime'] = (float) $value;
                break;
            default:
                $this->_throwException("δ֪��ģ������ѡ�� \"$name\"");
        }
    }

    /**
     * ͨ��ħ�������趨ģ�����
     *
     * @see    Template::set()
     * @param  string $name  ��������
     * @param  mixed  $value ����ֵ
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * ��ȡģ���ļ�
     *
     * @param  string $file ģ���ļ�����
     * @return string
     */
    public function getfile($file)
    {
        $cachefile = $this->_getCacheFile($file);
        if (!file_exists($cachefile))
            $this->cache($file);
        return $cachefile;
    }

    /**
     * ���ģ���ļ��Ƿ���Ҫ���»���
     *
     * @param  string  $file    ģ���ļ�����
     * @param  string  $md5data ģ���ļ� md5 У����Ϣ
     * @param  integer $md5data ģ���ļ�����ʱ��У����Ϣ
     * @return void
     */
    public function check($file, $md5data, $expireTime)
    {
        if ($this->_options['auto_update']
        && md5_file($this->_getTplFile($file)) != $md5data)
            $this->cache($file);
        if ($this->_options['cache_lifetime'] != 0
        && (time() - $expireTime >= $this->_options['cache_lifetime'] * 60))
            $this->cache($file);
    }

    /**
     * ��ģ���ļ����л���
     *
     * @param  string  $file    ģ���ļ�����
     * @return void
     */
    public function cache($file)
    {
        $tplfile = $this->_getTplFile($file);
        if (!is_readable($tplfile)) {
            $this->_throwException("ģ���ļ� \"$tplfile\" δ�ҵ������޷���");
        }

        //ȡ��ģ������
        $template = file_get_contents($tplfile);

        //���� <!--{}-->
        $template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);

        //�滻���԰�����
        //$template = preg_replace("/\{lang\s+(.+?)\}/ies", "languagevar('\\1')", $template);

        //�滻 PHP ���з�
        $template = str_replace("{LF}", "<?=\"\\n\"?>", $template);

        //�滻ֱ�ӱ������
        $varRegexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)"
                    . "(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
        $template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
        $template = preg_replace("/$varRegexp/es", "addquote('<?=\\1?>')", $template);
        $template = preg_replace("/\<\?\=\<\?\=$varRegexp\?\>\?\>/es", "addquote('<?=\\1?>')", $template);

        //�滻ģ����������
        $template = preg_replace(
            "/[\n\r\t]*\{template\s+([a-z0-9_]+)\}[\n\r\t]*/is",
            "\r\n<?php include(\$template->getfile('\\1')); ?>\r\n",
            $template
        );
        $template = preg_replace(
            "/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is",
            "\r\n<?php include(\$template->getfile(\\1)); ?>\r\n",
            $template
         );

        //�滻�ض�����
        $template = preg_replace(
            "/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/ies",
            "stripvtags('<?php \\1 ?>','')",
            $template
        );
        $template = preg_replace(
            "/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies",
            "stripvtags('<?php echo \\1; ?>','')",
            $template
        );
        $template = preg_replace(
            "/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies",
            "stripvtags('\\1<?php } elseif(\\2) { ?>\\3','')",
            $template
        );
        $template = preg_replace(
            "/([\n\r\t]*)\{else\}([\n\r\t]*)/is",
            "\\1<?php } else { ?>\\2",
            $template
        );

        //�滻ѭ�������������ж����
        $nest = 5;
        for ($i = 0; $i < $nest; $i++) {
            $template = preg_replace(
                "/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r]*(.+?)[\n\r]*\{\/loop\}[\n\r\t]*/ies",
                "stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\\3<?php } } ?>')",
                $template
            );
            //for ѭ��
            $template = preg_replace(
                "/[\n\r\t]*\{for\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/for\}[\n\r\t]*/ies",
                "stripvtags('<?php for(\\3=\\1;\\3<=\\2;\\3++) { ?>','\\4<?php } ?>')",
                $template
            );
            $template = preg_replace(
                "/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/ies",
                "stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\\4<?php } } ?>')",
                $template
            );
            $template = preg_replace(
                "/([\n\r\t]*)\{if\s+(.+?)\}([\n\r]*)(.+?)([\n\r]*)\{\/if\}([\n\r\t]*)/ies",
                "stripvtags('\\1<?php if(\\2) { ?>\\3','\\4\\5<?php } ?>\\6')",
                $template
            );
        }

        //�����滻
        $template = preg_replace(
            "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/s",
            "<?php echo \\1?>",
            $template
        );

        //ɾ�� PHP ����ϼ����Ŀո񼰻���
        $template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);

        //�����滻
        $template = preg_replace(
            "/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e",
            "transamp('\\0')",
            $template
        );
        $template = preg_replace(
            "/\<script[^\>]*?src=\"(.+?)\".*?\>\s*\<\/script\>/ise",
            "stripscriptamp('\\1')",
            $template
        );
        $template = preg_replace(
            "/[\n\r\t]*\{block\s+([a-zA-Z0-9_]+)\}(.+?)\{\/block\}/ies",
            "stripblock('\\1', '\\2')",
            $template
        );

        //��� md5 ������У��
        $md5data = md5_file($tplfile);
        $expireTime = time();
        $template = "<?php if (!class_exists('template')) die('Access Denied');"
                  . "\$template->getInstance()->check('$file', '$md5data', $expireTime);"
                  . "?>\r\n$template";

        $template = str_replace("<?=", "<?php echo ", $template);

        //д�뻺���ļ�
        $cachefile = $this->_getCacheFile($file);
        $makepath = $this->_makepath($cachefile);
        if ($makepath !== true)
            $this->_throwException("�޷���������Ŀ¼ \"$makepath\"");
        file_put_contents($cachefile, $template);
    }

    /**
     * ��·������Ϊ�ʺϲ���ϵͳ����ʽ
     *
     * @param  string $path ·������
     * @return string
     */
    protected function _trimpath($path)
    {
        return str_replace(array('/', '\\', '//', '\\\\'), self::DIR_SEP, $path);
    }

    /**
     * ��ȡģ���ļ�����·��
     *
     * @param  string $file ģ���ļ�����
     * @return string
     */
    protected function _getTplFile($file)
    {
        //ģ�����ļ���������ھ͵���default�µ�ģ���ļ�
        $f=$this->_options['template_dir'] . self::DIR_SEP . $file;
        $f2= ET_ROOT.'/templates/default/'.$file;
        if (!is_readable($f)) {
            $f=$f2;
        }
        return $this->_trimpath($f);
    }

    /**
     * ��ȡģ�建���ļ�����·��
     *
     * @param  string $file ģ���ļ�����
     * @return string
     */
    protected function _getCacheFile($file)
    {
        $file = preg_replace('/\.[a-z0-9\-_]+$/i', '.cache.php', $file);
        return $this->_trimpath($this->_options['cache_dir'] . self::DIR_SEP . $file);
    }

    /**
     * ����ָ����·�����������ڵ��ļ���
     *
     * @param  string  $path ·��/�ļ�������
     * @return string
     */
    protected function _makepath($path)
    {
        $dirs = explode(self::DIR_SEP, dirname($this->_trimpath($path)));
        $tmp = '';
        foreach ($dirs as $dir) {
            $tmp .= $dir . self::DIR_SEP;
            if (!file_exists($tmp) && !@mkdir($tmp, 0777))
                return $tmp;
        }
        return true;
    }

    /**
     * �׳�һ��������Ϣ
     *
     * @param string $message
     * @return void
     */
    protected function _throwException($message)
    {
        throw new Exception($message);
    }

}
?>