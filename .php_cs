<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('vendor')
    ->files()
    ->name('CheckCommand.php')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::NONE_LEVEL)
    ->fixers(
        [
            'psr0', // [PSR-0] Classes must be in a path that matches their namespace, be at least one namespace deep, and the class name should match the file name.
            'encoding', // [PSR-1] PHP code MUST use only UTF-8 without BOM (remove BOM).
            'short_tag', // [PSR-1] PHP code must use the long ?php ? tags or the short-echo ?= ? tags; it must not use the other tag variations.
            'elseif', // [PSR-2] The keyword elseif should be used instead of else if so that all control keywords looks like single words.
            'eof_ending', // [PSR-2] A file must always end with an empty line feed.
'-function_call_space', // [PSR-2] When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.
            'function_declaration', // [PSR-2] Spaces should be properly placed in a function declaration.
            'line_after_namespace', // [PSR-2] There MUST be one blank line after the namespace declaration.
            'linefeed', // [PSR-2] All PHP files must use the Unix LF (linefeed) line ending.
            'lowercase_constants', // [PSR-2] The PHP constants true, false, and null MUST be in lower case.
            'lowercase_keywords', // [PSR-2] PHP keywords MUST be in lower case.
            'method_argument_space', // [PSR-2] In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma.
            'multiple_use', // [PSR-2] There MUST be one use keyword per declaration.
'-parenthesis', // [PSR-2] There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.
            'php_closing_tag', // [PSR-2] The closing ? tag MUST be omitted from files containing only PHP.
'trailing_spaces', // [PSR-2] Remove trailing whitespace at the end of non-blank lines.
            'visibility', // [PSR-2] Visibility MUST be declared on all properties and methods; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.
            'concat_without_spaces', // [symfony] Concatenation should be used without spaces.
            'double_arrow_multiline_whitespaces', // [symfony] Operator => should not be arounded by multi-line whitespaces.
            'duplicate_semicolon', // [symfony] Remove duplicated semicolons.
'-extra_empty_lines', // [symfony] Removes extra empty lines.
            'include', // [symfony] Include and file path should be divided with a single space. File path should not be placed under brackets.
            'multiline_array_trailing_comma', // [symfony] PHP multi-line arrays should have a trailing comma.
            'namespace_no_leading_whitespace', // [symfony] The namespace declaration line shouldn't contain leading whitespace.
            'new_with_braces', // [symfony] All instances created with new keyword must be followed by braces.
            'object_operator', // [symfony] There should not be space before or after object T_OBJECT_OPERATOR.
            'operators_spaces', // [symfony] Operators should be arounded by at least one space.
            'phpdoc_params', // [symfony] All items of the @param phpdoc tags must be aligned vertically.
            'remove_leading_slash_use', // [symfony] Remove leading slashes in use clauses.
            'return', // [symfony] An empty line feed should precede a return statement.
            'spaces_cast', // [symfony] A single space should be between cast and variable.
            'standardize_not_equal', // [symfony] Replace all <> with !=.
            'ternary_spaces', // [symfony] Standardize spaces around ternary operator.
            'unused_use', // [symfony] Unused use statements must be removed.
            'whitespacy_lines', // [symfony] Remove trailing whitespace at the end of blank lines.
            'align_equals', // [contrib] Align equals symbols in consecutive lines.
            'align_double_arrow', // [contrib] Align double arrow symbols in consecutive lines.


            // future fixes
            '-short_array_syntax', // [contrib] PHP array's should use the PHP 5.4 short-syntax.

            // removed fixers
            '-single_array_no_trailing_comma', // [symfony] PHP single-line arrays should not have trailing comma.
            '-multiline_spaces_before_semicolon', // [contrib] Multi-line whitespace before closing semicolon are prohibited.
            '-concat_with_spaces', // [contrib] Concatenation should be used with at least one whitespace around.
            '-remove_lines_between_uses', // [symfony] Removes line breaks between use statements.
            '-empty_return', // [symfony] A return statement wishing to return nothing should be simply "return".
            '-spaces_before_semicolon', // [symfony] Single-line whitespace before closing semicolon are prohibited.
            '-indentation', // [PSR-2] Code MUST use an indent of 4 spaces, and MUST NOT use tabs for indenting.
            '-braces', // [PSR-2] The body of each structure MUST be enclosed by braces. Braces should be properly placed. Body of braces should be properly indented.
            '-ordered_use', // [contrib] Ordering use statements.
            '-strict', // [contrib] Comparison should be strict. Warning! This could change code behavior.
            '-strict_param', // [contrib] Functions should be used with $strict param. Warning! This could change code behavior.

        ]
    )
    ->finder($finder)
;