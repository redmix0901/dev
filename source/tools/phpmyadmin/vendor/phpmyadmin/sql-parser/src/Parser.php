<?php

/**
 * Defines the parser of the library.
 *
 * This is one of the most important components, along with the lexer.
 */

namespace PhpMyAdmin\SqlParser;

use PhpMyAdmin\SqlParser\Exceptions\ParserException;
use PhpMyAdmin\SqlParser\Statements\SelectStatement;
use PhpMyAdmin\SqlParser\Statements\TransactionStatement;

/**
 * Takes multiple tokens (contained in a Lexer instance) as input and builds a
 * parse tree.
 *
 * @category Parser
 *
 * @license  https://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 */
class Parser extends Core
{
    /**
     * Array of classes that are used in parsing the SQL statements.
     *
     * @var array
     */
    public static $STATEMENT_PARSERS = array(
        // MySQL Utility Statements
        'DESCRIBE' => 'PhpMyAdmin\\SqlParser\\Statements\\ExplainStatement',
        'DESC' => 'PhpMyAdmin\\SqlParser\\Statements\\ExplainStatement',
        'EXPLAIN' => 'PhpMyAdmin\\SqlParser\\Statements\\ExplainStatement',
        'FLUSH' => '',
        'GRANT' => '',
        'HELP' => '',
        'SET PASSWORD' => '',
        'STATUS' => '',
        'USE' => '',

        // Table Maintenance Statements
        // https://dev.mysql.com/doc/refman/5.7/en/table-maintenance-sql.html
        'ANALYZE' => 'PhpMyAdmin\\SqlParser\\Statements\\AnalyzeStatement',
        'BACKUP' => 'PhpMyAdmin\\SqlParser\\Statements\\BackupStatement',
        'CHECK' => 'PhpMyAdmin\\SqlParser\\Statements\\CheckStatement',
        'CHECKSUM' => 'PhpMyAdmin\\SqlParser\\Statements\\ChecksumStatement',
        'OPTIMIZE' => 'PhpMyAdmin\\SqlParser\\Statements\\OptimizeStatement',
        'REPAIR' => 'PhpMyAdmin\\SqlParser\\Statements\\RepairStatement',
        'RESTORE' => 'PhpMyAdmin\\SqlParser\\Statements\\RestoreStatement',

        // Database Administration Statements
        // https://dev.mysql.com/doc/refman/5.7/en/sql-syntax-server-administration.html
        'SET' => 'PhpMyAdmin\\SqlParser\\Statements\\SetStatement',
        'SHOW' => 'PhpMyAdmin\\SqlParser\\Statements\\ShowStatement',

        // Data Definition Statements.
        // https://dev.mysql.com/doc/refman/5.7/en/sql-syntax-data-definition.html
        'ALTER' => 'PhpMyAdmin\\SqlParser\\Statements\\AlterStatement',
        'CREATE' => 'PhpMyAdmin\\SqlParser\\Statements\\CreateStatement',
        'DROP' => 'PhpMyAdmin\\SqlParser\\Statements\\DropStatement',
        'RENAME' => 'PhpMyAdmin\\SqlParser\\Statements\\RenameStatement',
        'TRUNCATE' => 'PhpMyAdmin\\SqlParser\\Statements\\TruncateStatement',

        // Data Manipulation Statements.
        // https://dev.mysql.com/doc/refman/5.7/en/sql-syntax-data-manipulation.html
        'CALL' => 'PhpMyAdmin\\SqlParser\\Statements\\CallStatement',
        'DELETE' => 'PhpMyAdmin\\SqlParser\\Statements\\DeleteStatement',
        'DO' => '',
        'HANDLER' => '',
        'INSERT' => 'PhpMyAdmin\\SqlParser\\Statements\\InsertStatement',
        'LOAD DATA' => 'PhpMyAdmin\\SqlParser\\Statements\\LoadStatement',
        'REPLACE' => 'PhpMyAdmin\\SqlParser\\Statements\\ReplaceStatement',
        'SELECT' => 'PhpMyAdmin\\SqlParser\\Statements\\SelectStatement',
        'UPDATE' => 'PhpMyAdmin\\SqlParser\\Statements\\UpdateStatement',

        // Prepared Statements.
        // https://dev.mysql.com/doc/refman/5.7/en/sql-syntax-prepared-statements.html
        'DEALLOCATE' => '',
        'EXECUTE' => '',
        'PREPARE' => '',

        // Transactional and Locking Statements
        // https://dev.mysql.com/doc/refman/5.7/en/commit.html
        'BEGIN' => 'PhpMyAdmin\\SqlParser\\Statements\\TransactionStatement',
        'COMMIT' => 'PhpMyAdmin\\SqlParser\\Statements\\TransactionStatement',
        'ROLLBACK' => 'PhpMyAdmin\\SqlParser\\Statements\\TransactionStatement',
        'START TRANSACTION' => 'PhpMyAdmin\\SqlParser\\Statements\\TransactionStatement',
    );

    /**
     * Array of classes that are used in parsing SQL components.
     *
     * @var array
     */
    public static $KEYWORD_PARSERS = array(
        // This is not a proper keyword and was added here to help the
        // formatter.
        'PARTITION BY' => array(),
        'SUBPARTITION BY' => array(),

        // This is not a proper keyword and was added here to help the
        // builder.
        '_OPTIONS' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\OptionsArray',
            'field' => 'options',
        ),
        '_END_OPTIONS' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\OptionsArray',
            'field' => 'end_options',
        ),

        'INTERSECT' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\UnionKeyword',
            'field' => 'union',
        ),
        'EXCEPT' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\UnionKeyword',
            'field' => 'union',
        ),
        'UNION' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\UnionKeyword',
            'field' => 'union',
        ),
        'UNION ALL' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\UnionKeyword',
            'field' => 'union',
        ),
        'UNION DISTINCT' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\UnionKeyword',
            'field' => 'union',
        ),

        // Actual clause parsers.
        'ALTER' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\Expression',
            'field' => 'table',
            'options' => array('parseField' => 'table'),
        ),
        'ANALYZE' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'tables',
            'options' => array('parseField' => 'table'),
        ),
        'BACKUP' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'tables',
            'options' => array('parseField' => 'table'),
        ),
        'CALL' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\FunctionCall',
            'field' => 'call',
        ),
        'CHECK' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'tables',
            'options' => array('parseField' => 'table'),
        ),
        'CHECKSUM' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'tables',
            'options' => array('parseField' => 'table'),
        ),
        'CROSS JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'DROP' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'fields',
            'options' => array('parseField' => 'table'),
        ),
        'FROM' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'from',
            'options' => array('field' => 'table'),
        ),
        'GROUP BY' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\GroupKeyword',
            'field' => 'group',
        ),
        'HAVING' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\Condition',
            'field' => 'having',
        ),
        'INTO' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\IntoKeyword',
            'field' => 'into',
        ),
        'JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'LEFT JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'LEFT OUTER JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'ON' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\Expression',
            'field' => 'table',
            'options' => array('parseField' => 'table'),
        ),
        'RIGHT JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'RIGHT OUTER JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'INNER JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'FULL JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'FULL OUTER JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'NATURAL JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'NATURAL LEFT JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'NATURAL RIGHT JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'NATURAL LEFT OUTER JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'NATURAL RIGHT OUTER JOIN' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\JoinKeyword',
            'field' => 'join',
        ),
        'LIMIT' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\Limit',
            'field' => 'limit',
        ),
        'OPTIMIZE' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'tables',
            'options' => array('parseField' => 'table'),
        ),
        'ORDER BY' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\OrderKeyword',
            'field' => 'order',
        ),
        'PARTITION' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ArrayObj',
            'field' => 'partition',
        ),
        'PROCEDURE' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\FunctionCall',
            'field' => 'procedure',
        ),
        'RENAME' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\RenameOperation',
            'field' => 'renames',
        ),
        'REPAIR' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'tables',
            'options' => array('parseField' => 'table'),
        ),
        'RESTORE' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'tables',
            'options' => array('parseField' => 'table'),
        ),
        'SET' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\SetOperation',
            'field' => 'set',
        ),
        'SELECT' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'expr',
        ),
        'TRUNCATE' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\Expression',
            'field' => 'table',
            'options' => array('parseField' => 'table'),
        ),
        'UPDATE' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\ExpressionArray',
            'field' => 'tables',
            'options' => array('parseField' => 'table'),
        ),
        'VALUE' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\Array2d',
            'field' => 'values',
        ),
        'VALUES' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\Array2d',
            'field' => 'values',
        ),
        'WHERE' => array(
            'class' => 'PhpMyAdmin\\SqlParser\\Components\\Condition',
            'field' => 'where',
        ),
    );

    /**
     * The list of tokens that are parsed.
     *
     * @var TokensList
     */
    public $list;

    /**
     * List of statements parsed.
     *
     * @var Statement[]
     */
    public $statements = array();

    /**
     * The number of opened brackets.
     *
     * @var int
     */
    public $brackets = 0;

    /**
     * Constructor.
     *
     * @param string|UtfString|TokensList $list   the list of tokens to be parsed
     * @param bool                        $strict whether strict mode should be enabled or not
     */
    public function __construct($list = null, $strict = false)
    {
        if ((is_string($list)) || ($list instanceof UtfString)) {
            $lexer = new Lexer($list, $strict);
            $this->list = $lexer->list;
        } elseif ($list instanceof TokensList) {
            $this->list = $list;
        }

        $this->strict = $strict;

        if ($list !== null) {
            $this->parse();
        }
    }

    /**
     * Builds the parse trees.
     */
    public function parse()
    {
        /**
         * Last transaction.
         *
         * @var TransactionStatement
         */
        $lastTransaction = null;

        /**
         * Last parsed statement.
         *
         * @var Statement
         */
        $lastStatement = null;

        /**
         * Union's type or false for no union.
         *
         * @var bool|string
         */
        $unionType = false;

        /**
         * The index of the last token from the last statement.
         *
         * @var int
         */
        $prevLastIdx = -1;

        /**
         * The list of tokens.
         *
         * @var TokensList
         */
        $list = &$this->list;

        for (; $list->idx < $list->count; ++$list->idx) {
            /**
             * Token parsed at this moment.
             *
             * @var Token
             */
            $token = $list->tokens[$list->idx];

            // `DELIMITER` is not an actual statement and it requires
            // special handling.
            if (($token->type === Token::TYPE_NONE)
                && (strtoupper($token->token) === 'DELIMITER')
            ) {
                // Skipping to the end of this statement.
                $list->getNextOfType(Token::TYPE_DELIMITER);
                $prevLastIdx = $list->idx;
                continue;
            }

            // Counting the brackets around statements.
            if ($token->value === '(') {
                ++$this->brackets;
                continue;
            }

            // Statements can start with keywords only.
            // Comments, whitespaces, etc. are ignored.
            if ($token->type !== Token::TYPE_KEYWORD) {
                if (($token->type !== TOKEN::TYPE_COMMENT)
                    && ($token->type !== Token::TYPE_WHITESPACE)
                    && ($token->type !== Token::TYPE_OPERATOR) // `(` and `)`
                    && ($token->type !== Token::TYPE_DELIMITER)
                ) {
                    $this->error(
                        'Unexpected beginning of statement.',
                        $token
                    );
                }
                continue;
            }

            if (($token->keyword === 'UNION') ||
                    ($token->keyword === 'UNION ALL') ||
                    ($token->keyword === 'UNION DISTINCT') ||
                    ($token->keyword === 'EXCEPT') ||
                    ($token->keyword === 'INTERSECT')
            ) {
                $unionType = $token->keyword;
                continue;
            }

            // Checking if it is a known statement that can be parsed.
            if (empty(static::$STATEMENT_PARSERS[$token->keyword])) {
                if (!isset(static::$STATEMENT_PARSERS[$token->keyword])) {
                    // A statement is considered recognized if the parser
                    // is aware that it is a statement, but it does not have
                    // a parser for it yet.
                    $this->error(
                        'Unrecognized statement type.',
                        $token
                    );
                }
                // Skipping to the end of this statement.
                $list->getNextOfType(Token::TYPE_DELIMITER);
                $prevLastIdx = $list->idx;
                continue;
            }

            /**
             * The name of the class that is used for parsing.
             *
             * @var string
             */
            $class = static::$STATEMENT_PARSERS[$token->keyword];

            /**
             * Processed statement.
             *
             * @var Statement
             */
            $statement = new $class($this, $this->list);

            // The first token that is a part of this token is the next token
            // unprocessed by the previous statement.
            // There might be brackets around statements and this shouldn't
            // affect the parser
            $statement->first = $prevLastIdx + 1;

            // Storing the index of the last token parsed and updating the old
            // index.
            $statement->last = $list->idx;
            $prevLastIdx = $list->idx;

            // Handles unions.
            if ((!empty($unionType))
                && ($lastStatement instanceof SelectStatement)
                && ($statement instanceof SelectStatement)
            ) {
                /*
                 * This SELECT statement.
                 *
                 * @var SelectStatement $statement
                 */

                /*
                 * Last SELECT statement.
                 *
                 * @var SelectStatement $lastStatement
                 */
                $lastStatement->union[] = array($unionType, $statement);

                // if there are no no delimiting brackets, the `ORDER` and
                // `LIMIT` keywords actually belong to the first statement.
                $lastStatement->order = $statement->order;
                $lastStatement->limit = $statement->limit;
                $statement->order = array();
                $statement->limit = null;

                // The statement actually ends where the last statement in
                // union ends.
                $lastStatement->last = $statement->last;

                $unionType = false;

                // Validate clause order
                $statement->validateClauseOrder($this, $list);
                continue;
            }

            // Handles transactions.
            if ($statement instanceof TransactionStatement) {
                /*
                 * @var TransactionStatement
                 */
                if ($statement->type === TransactionStatement::TYPE_BEGIN) {
                    $lastTransaction = $statement;
                    $this->statements[] = $statement;
                } elseif ($statement->type === TransactionStatement::TYPE_END) {
                    if ($lastTransaction === null) {
                        // Even though an error occurred, the query is being
                        // saved.
                        $this->statements[] = $statement;
                        $this->error(
                            'No transaction was previously started.',
                            $token
                        );
                    } else {
                        $lastTransaction->end = $statement;
                    }
                    $lastTransaction = null;
                }

                // Validate clause order
                $statement->validateClauseOrder($this, $list);
                continue;
            }

            // Validate clause order
            $statement->validateClauseOrder($this, $list);

            // Finally, storing the statement.
            if ($lastTransaction !== null) {
                $lastTransaction->statements[] = $statement;
            } else {
                $this->statements[] = $statement;
            }
            $lastStatement = $statement;
        }
    }

    /**
     * Creates a new error log.
     *
     * @param string $msg   the error message
     * @param Token  $token the token that produced the error
     * @param int    $code  the code of the error
     *
     * @throws ParserException throws the exception, if strict mode is enabled
     */
    public function error($msg, Token $token = null, $code = 0)
    {
        $error = new ParserException(
            Translator::gettext($msg),
            $token, $code
        );
        parent::error($error);
    }
}
