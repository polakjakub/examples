<?php

/**
 * Class OutputBase
 * Class for fetching data from DB and offering them as a XML
 */
class OutputBase
{
    protected $db;
    protected $xml = '';

    public function __construct()
    {
        $this->db = db_mysql::singleton();
    }

    /**
     * This returns XML for given query
     * @param string $query
     * @param string $rowBracket
     * @return string
     */
    public function queryToXML($query, $rowBracket)
    {
        $xml = '';
        $result = $this->db->query($query);
        while (false !== ($row = $this->db->fetchAssoc($result))) {
            $xml .= $this->createElement($rowBracket, $this->parseRow($row));
        }
        return $xml;
    }

    /**
     * Apply callbacks for one row
     * @param $row
     * @return string
     */
    protected function parseRow($row)
    {
        $xml = '';
        foreach ($row as $colName => $value) {
            if (isset($this->callbacks[$colName])) {
                foreach ($this->callbacks[$colName] as $callback) {
                    if (is_callable(array($this, $callback['function']))) {
                        $this->$callback['function']($value, $callback['parameters']);
                    }
                }
            }
            $xml .= sprintf('<%1$s>%2$s</%1$s>', $colName, $value);
        }
        return $xml;
    }

    /**
     * Set callback function
     * @param $colName
     * @param $function
     * @param null $parameters
     */
    public function addCallback($colName, $function, $parameters = null)
    {
        if (!isset($this->callbacks[$colName])) {
            $this->callbacks[$colName] = array();
        }
        $this->callbacks[$colName][] = array('function' => $function, 'parameters' => $parameters);
    }

    /**
     * Purge all callback functions
     */
    public function clearAllCallbacks()
    {
        $this->callbacks = array();
    }

    /**
     * Creates a XML document
     * @param $name
     * @param null $content
     * @param bool $addCDATA
     * @return string
     */
    public static function createElement($name, $content = null, $addCDATA = false)
    {
        if ($content === null) {
            return sprintf('<%1$s />', $name);
        }
        if ($addCDATA) {
            self::addCDATA($content);
        }
        return sprintf('<%1$s>%2$s</%1$s>', $name, $content);
    }

    /**
     * Callback function to add CDATA
     * @param $value
     */
    public static function addCDATA(&$value)
    {
        $value = '<![CDATA[' . $value . ']]>';
    }

    /**
     * Callback function urlencode
     * @param $value
     */
    public static function URLEncode(&$url)
    {
        $url = urlencode($url);
    }
}

?>
