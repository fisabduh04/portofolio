---
paths:
  - '**/*'
---

# General

## Keep work and verification focused on the affected scope
Process only files and behavior directly affected by the current request. Run the narrowest relevant tests (use --filter for specific cases); reuse prior passing results for unchanged behavior. Do not rerun unrelated tests or broaden verification without a concrete dependency, failure, or risk that requires it. Avoid repetitive polling, redundant tool calls, and repeated progress messages; keep output and explanations concise.
