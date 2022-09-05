<?php declare(strict_types=1);

namespace Becklyn\Eventor\Application;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-09-05
 */
class TraceContext
{
    public function __construct(public readonly string $traceParent, public readonly string $traceState)
    {}
}
