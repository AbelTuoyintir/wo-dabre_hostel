## 2026-07-26 - Paystack Webhook Webhook Signature Key Inconsistency and PHP Coalescing Division Priority Precedence

**Vulnerability:** Retrieving an incorrect, non-existent configuration key `paystack.secret` instead of `paystack.secretKey` during Paystack webhook signature verification caused signature verification to fall back to null, leading to potential authentication bypass or execution with null keys. At the same time, PHP operator precedence division division priority over null-coalescing (`??`) without proper parentheses caused uncaught `ErrorException: Undefined array key` when metadata payloads didn't contain precise values.

**Learning:** Webhook verification should never rely on non-existent configuration namespaces, and PHP operator precedence assigns division higher precedence than the null-coalesce operator.

**Prevention:** Always verify config key alignments across service files, use exact parenthesized groupings around mathematical operations mixed with null-coalesce operator arrays, and ensure errors in webhook processors do not get swallowed but rather bubble up properly to ensure transaction safe rollback.
