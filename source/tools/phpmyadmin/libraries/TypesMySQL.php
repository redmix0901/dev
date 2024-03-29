<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * SQL data types definition
 *
 * @package PhpMyAdmin
 */
namespace PMA\libraries;

/**
 * Class holding type definitions for MySQL.
 *
 * @package PhpMyAdmin
 */
class TypesMySQL extends Types
{
    /**
     * Returns the data type description.
     *
     * @param string $type The data type to get a description.
     *
     * @return string
     *
     */
    public function getTypeDescription($type)
    {
        $type = mb_strtoupper($type);
        switch ($type) {
        case 'TINYINT':
            return __(
                'A 1-byte integer, signed range is -128 to 127, unsigned range is ' .
                '0 to 255'
            );
        case 'SMALLINT':
            return __(
                'A 2-byte integer, signed range is -32,768 to 32,767, unsigned ' .
                'range is 0 to 65,535'
            );
        case 'MEDIUMINT':
            return __(
                'A 3-byte integer, signed range is -8,388,608 to 8,388,607, ' .
                'unsigned range is 0 to 16,777,215'
            );
        case 'INT':
            return __(
                'A 4-byte integer, signed range is ' .
                '-2,147,483,648 to 2,147,483,647, unsigned range is 0 to ' .
                '4,294,967,295'
            );
        case 'BIGINT':
            return __(
                'An 8-byte integer, signed range is -9,223,372,036,854,775,808 ' .
                'to 9,223,372,036,854,775,807, unsigned range is 0 to ' .
                '18,446,744,073,709,551,615'
            );
        case 'DECIMAL':
            return __(
                'A fixed-point number (M, D) - the maximum number of digits (M) ' .
                'is 65 (default 10), the maximum number of decimals (D) is 30 ' .
                '(default 0)'
            );
        case 'FLOAT':
            return __(
                'A small floating-point number, allowable values are ' .
                '-3.402823466E+38 to -1.175494351E-38, 0, and 1.175494351E-38 to ' .
                '3.402823466E+38'
            );
        case 'DOUBLE':
            return __(
                'A double-precision floating-point number, allowable values are ' .
                '-1.7976931348623157E+308 to -2.2250738585072014E-308, 0, and ' .
                '2.2250738585072014E-308 to 1.7976931348623157E+308'
            );
        case 'REAL':
            return __(
                'Synonym for DOUBLE (exception: in REAL_AS_FLOAT SQL mode it is ' .
                'a synonym for FLOAT)'
            );
        case 'BIT':
            return __(
                'A bit-field type (M), storing M of bits per value (default is 1, ' .
                'maximum is 64)'
            );
        case 'BOOLEAN':
            return __(
                'A synonym for TINYINT(1), a value of zero is considered false, ' .
                'nonzero values are considered true'
            );
        case 'SERIAL':
            return __('An alias for BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE');
        case 'DATE':
            return sprintf(
                __('A date, supported range is %1$s to %2$s'), '1000-01-01',
                '9999-12-31'
            );
        case 'DATETIME':
            return sprintf(
                __('A date and time combination, supported range is %1$s to %2$s'),
                '1000-01-01 00:00:00', '9999-12-31 23:59:59'
            );
        case 'TIMESTAMP':
            return __(
                'A timestamp, range is 1970-01-01 00:00:01 UTC to 2038-01-09 ' .
                '03:14:07 UTC, stored as the number of seconds since the epoch ' .
                '(1970-01-01 00:00:00 UTC)'
            );
        case 'TIME':
            return sprintf(
                __('A time, range is %1$s to %2$s'), '-838:59:59', '838:59:59'
            );
        case 'YEAR':
            return __(
                "A year in four-digit (4, default) or two-digit (2) format, the " .
                "allowable values are 70 (1970) to 69 (2069) or 1901 to 2155 and " .
                "0000"
            );
        case 'CHAR':
            return __(
                'A fixed-length (0-255, default 1) string that is always ' .
                'right-padded with spaces to the specified length when stored'
            );
        case 'VARCHAR':
            return sprintf(
                __(
                    'A variable-length (%s) string, the effective maximum length ' .
                    'is subject to the maximum row size'
                ), '0-65,535'
            );
        case 'TINYTEXT':
            return __(
                'A TEXT column with a maximum length of 255 (2^8 - 1) characters, ' .
                'stored with a one-byte prefix indicating the length of the value ' .
                'in bytes'
            );
        case 'TEXT':
            return __(
                'A TEXT column with a maximum length of 65,535 (2^16 - 1) ' .
                'characters, stored with a two-byte prefix indicating the length ' .
                'of the value in bytes'
            );
        case 'MEDIUMTEXT':
            return __(
                'A TEXT column with a maximum length of 16,777,215 (2^24 - 1) ' .
                'characters, stored with a three-byte prefix indicating the ' .
                'length of the value in bytes'
            );
        case 'LONGTEXT':
            return __(
                'A TEXT column with a maximum length of 4,294,967,295 or 4GiB ' .
                '(2^32 - 1) characters, stored with a four-byte prefix indicating ' .
                'the length of the value in bytes'
            );
        case 'BINARY':
            return __(
                'Similar to the CHAR type, but stores binary byte strings rather ' .
                'than non-binary character strings'
            );
        case 'VARBINARY':
            return __(
                'Similar to the VARCHAR type, but stores binary byte strings ' .
                'rather than non-binary character strings'
            );
        case 'TINYBLOB':
            return __(
                'A BLOB column with a maximum length of 255 (2^8 - 1) bytes, ' .
                'stored with a one-byte prefix indicating the length of the value'
            );
        case 'MEDIUMBLOB':
            return __(
                'A BLOB column with a maximum length of 16,777,215 (2^24 - 1) ' .
                'bytes, stored with a three-byte prefix indicating the length of ' .
                'the value'
            );
        case 'BLOB':
            return __(
                'A BLOB column with a maximum length of 65,535 (2^16 - 1) bytes, ' .
                'stored with a two-byte prefix indicating the length of the value'
            );
        case 'LONGBLOB':
            return __(
                'A BLOB column with a maximum length of 4,294,967,295 or 4GiB ' .
                '(2^32 - 1) bytes, stored with a four-byte prefix indicating the ' .
                'length of the value'
            );
        case 'ENUM':
            return __(
                "An enumeration, chosen from the list of up to 65,535 values or " .
                "the special '' error value"
            );
        case 'SET':
            return __("A single value chosen from a set of up to 64 members");
        case 'GEOMETRY':
            return __('A type that can store a geometry of any type');
        case 'POINT':
            return __('A point in 2-dimensional space');
        case 'LINESTRING':
            return __('A curve with linear interpolation between points');
        case 'POLYGON':
            return __('A polygon');
        case 'MULTIPOINT':
            return __('A collection of points');
        case 'MULTILINESTRING':
            return __(
                'A collection of curves with linear interpolation between points'
            );
        case 'MULTIPOLYGON':
            return __('A collection of polygons');
        case 'GEOMETRYCOLLECTION':
            return __('A collection of geometry objects of any type');
        case 'JSON':
            return __(
                'Stores and enables efficient access to data in JSON'
                . ' (JavaScript Object Notation) documents'
            );
        }
        return '';
    }

    /**
     * Returns class of a type, used for functions available for type
     * or default values.
     *
     * @param string $type The data type to get a class.
     *
     * @return string
     *
     */
    public function getTypeClass($type)
    {
        $type = mb_strtoupper($type);
        switch ($type) {
        case 'TINYINT':
        case 'SMALLINT':
        case 'MEDIUMINT':
        case 'INT':
        case 'BIGINT':
        case 'DECIMAL':
        case 'FLOAT':
        case 'DOUBLE':
        case 'REAL':
        case 'BIT':
        case 'BOOLEAN':
        case 'SERIAL':
            return 'NUMBER';

        case 'DATE':
        case 'DATETIME':
        case 'TIMESTAMP':
        case 'TIME':
        case 'YEAR':
            return 'DATE';

        case 'CHAR':
        case 'VARCHAR':
        case 'TINYTEXT':
        case 'TEXT':
        case 'MEDIUMTEXT':
        case 'LONGTEXT':
        case 'BINARY':
        case 'VARBINARY':
        case 'TINYBLOB':
        case 'MEDIUMBLOB':
        case 'BLOB':
        case 'LONGBLOB':
        case 'ENUM':
        case 'SET':
            return 'CHAR';

        case 'GEOMETRY':
        case 'POINT':
        case 'LINESTRING':
        case 'POLYGON':
        case 'MULTIPOINT':
        case 'MULTILINESTRING':
        case 'MULTIPOLYGON':
        case 'GEOMETRYCOLLECTION':
            return 'SPATIAL';

        case 'JSON':
            return 'JSON';
        }

        return '';
    }

    /**
     * Returns array of functions available for a class.
     *
     * @param string $class The class to get function list.
     *
     * @return string[]
     *
     */
    public function getFunctionsClass($class)
    {
        switch ($class) {
        case 'CHAR':
            $ret = array(
                'AES_DECRYPT',
                'AES_ENCRYPT',
                'BIN',
                'CHAR',
                'COMPRESS',
                'CURRENT_USER',
                'DATABASE',
                'DAYNAME',
                'DES_DECRYPT',
                'DES_ENCRYPT',
                'ENCRYPT',
                'HEX',
                'INET6_NTOA',
                'INET_NTOA',
                'LOAD_FILE',
                'LOWER',
                'LTRIM',
                'MD5',
                'MONTHNAME',
                'OLD_PASSWORD',
                'PASSWORD',
                'QUOTE',
                'REVERSE',
                'RTRIM',
                'SHA1',
                'SOUNDEX',
                'SPACE',
                'TRIM',
                'UNCOMPRESS',
                'UNHEX',
                'UPPER',
                'USER',
                'UUID',
                'VERSION',
            );

            if ((PMA_MARIADB && PMA_MYSQL_INT_VERSION < 100012)
                || PMA_MYSQL_INT_VERSION < 50603
            ) {
                $ret = array_diff($ret, array('INET6_NTOA'));
            }
            return $ret;

        case 'DATE':
            return array(
                'CURRENT_DATE',
                'CURRENT_TIME',
                'DATE',
                'FROM_DAYS',
                'FROM_UNIXTIME',
                'LAST_DAY',
                'NOW',
                'SEC_TO_TIME',
                'SYSDATE',
                'TIME',
                'TIMESTAMP',
                'UTC_DATE',
                'UTC_TIME',
                'UTC_TIMESTAMP',
                'YEAR',
            );

        case 'NUMBER':
            $ret = array(
                'ABS',
                'ACOS',
                'ASCII',
                'ASIN',
                'ATAN',
                'BIT_LENGTH',
                'BIT_COUNT',
                'CEILING',
                'CHAR_LENGTH',
                'CONNECTION_ID',
                'COS',
                'COT',
                'CRC32',
                'DAYOFMONTH',
                'DAYOFWEEK',
                'DAYOFYEAR',
                'DEGREES',
                'EXP',
                'FLOOR',
                'HOUR',
                'INET6_ATON',
                'INET_ATON',
                'LENGTH',
                'LN',
                'LOG',
                'LOG2',
                'LOG10',
                'MICROSECOND',
                'MINUTE',
                'MONTH',
                'OCT',
                'ORD',
                'PI',
                'QUARTER',
                'RADIANS',
                'RAND',
                'ROUND',
                'SECOND',
                'SIGN',
                'SIN',
                'SQRT',
                'TAN',
                'TO_DAYS',
                'TO_SECONDS',
                'TIME_TO_SEC',
                'UNCOMPRESSED_LENGTH',
                'UNIX_TIMESTAMP',
                'UUID_SHORT',
                'WEEK',
                'WEEKDAY',
                'WEEKOFYEAR',
                'YEARWEEK',
            );
            if ((PMA_MARIADB && PMA_MYSQL_INT_VERSION < 100012)
                || PMA_MYSQL_INT_VERSION < 50603
            ) {
                $ret = array_diff($ret, array('INET6_ATON'));
            }
            return $ret;

        case 'SPATIAL':
            return array(
                'GeomFromText',
                'GeomFromWKB',

                'GeomCollFromText',
                'LineFromText',
                'MLineFromText',
                'PointFromText',
                'MPointFromText',
                'PolyFromText',
                'MPolyFromText',

                'GeomCollFromWKB',
                'LineFromWKB',
                'MLineFromWKB',
                'PointFromWKB',
                'MPointFromWKB',
                'PolyFromWKB',
                'MPolyFromWKB',
            );

        }
        return array();
    }

    /**
     * Returns array of all attributes available.
     *
     * @return string[]
     *
     */
    public function getAttributes()
    {
        return array(
            '',
            'BINARY',
            'UNSIGNED',
            'UNSIGNED ZEROFILL',
            'on update CURRENT_TIMESTAMP',
        );
    }

    /**
     * Returns array of all column types available.
     *
     * VARCHAR, TINYINT, TEXT and DATE are listed first, based on
     * estimated popularity.
     *
     * @return string[]
     *
     */
    public function getColumns()
    {
        $ret = parent::getColumns();
        // numeric
        $ret[_pgettext('numeric types', 'Numeric')] = array(
            'TINYINT',
            'SMALLINT',
            'MEDIUMINT',
            'INT',
            'BIGINT',
            '-',
            'DECIMAL',
            'FLOAT',
            'DOUBLE',
            'REAL',
            '-',
            'BIT',
            'BOOLEAN',
            'SERIAL',
        );

        // Date/Time
        $ret[_pgettext('date and time types', 'Date and time')] = array(
            'DATE',
            'DATETIME',
            'TIMESTAMP',
            'TIME',
            'YEAR',
        );

        // Text
        $ret[_pgettext('string types', 'String')] = array(
            'CHAR',
            'VARCHAR',
            '-',
            'TINYTEXT',
            'TEXT',
            'MEDIUMTEXT',
            'LONGTEXT',
            '-',
            'BINARY',
            'VARBINARY',
            '-',
            'TINYBLOB',
            'MEDIUMBLOB',
            'BLOB',
            'LONGBLOB',
            '-',
            'ENUM',
            'SET',
        );

        $ret[_pgettext('spatial types', 'Spatial')] = array(
            'GEOMETRY',
            'POINT',
            'LINESTRING',
            'POLYGON',
            'MULTIPOINT',
            'MULTILINESTRING',
            'MULTIPOLYGON',
            'GEOMETRYCOLLECTION',
        );

        if ((PMA_MYSQL_INT_VERSION >= 50708 && \PMA\libraries\Util::getServerType() != 'MariaDB') ||
            (PMA_MYSQL_INT_VERSION >= 100207 && \PMA\libraries\Util::getServerType() == 'MariaDB')
        ) {
          $ret['JSON'] = array(
              'JSON',
          );
        }

        return $ret;
    }

    /**
     * Returns an array of integer types
     *
     * @return string[] integer types
     */
    public function getIntegerTypes()
    {
        return array('tinyint', 'smallint', 'mediumint', 'int', 'bigint');
    }

    /**
     * Returns the min and max values of a given integer type
     *
     * @param string  $type   integer type
     * @param boolean $signed whether signed
     *
     * @return string[] min and max values
     */
    public function getIntegerRange($type, $signed = true)
    {
        static $min_max_data = array(
            'unsigned' => array(
                'tinyint'   => array('0', '255'),
                'smallint'  => array('0', '65535'),
                'mediumint' => array('0', '16777215'),
                'int'       => array('0', '4294967295'),
                'bigint'    => array('0', '18446744073709551615')
            ),
            'signed' => array(
                'tinyint'   => array('-128', '127'),
                'smallint'  => array('-32768', '32767'),
                'mediumint' => array('-8388608', '8388607'),
                'int'       => array('-2147483648', '2147483647'),
                'bigint'    => array('-9223372036854775808', '9223372036854775807')
            )
        );
        $relevantArray = $signed
            ? $min_max_data['signed']
            : $min_max_data['unsigned'];
        return isset($relevantArray[$type]) ? $relevantArray[$type] : array('', '');
    }
}
