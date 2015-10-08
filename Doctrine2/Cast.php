<?php
namespace Enova\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

/**
 *    @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 *    @author    Marco Aurélio Simão <marco.a.simao@gmail.com>
 *    @company   Enova Interactive
 */
class Cast extends FunctionNode
{
    public $firstDateExpression = null;
    public $unit = null;    

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstDateExpression = $parser->StringPrimary();

        $parser->match(Lexer::T_AS);

        $parser->match(Lexer::T_IDENTIFIER);
        $lexer = $parser->getLexer();
        $this->unit = $lexer->token['value'];

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return sprintf('CAST(%s AS %s)',  $this->firstDateExpression->dispatch($sqlWalker), $this->unit);
    }
}
