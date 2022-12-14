# 3.0.0

- (breaking) The Dapr subscription registry no longer catches exceptions in topic subscribers and lets them bubble up the call stack. Any further subscribers are not executed. Previously, the exceptions were caught, other subscribers were called and a 400 response was returned at the end.

# 2.0.1

- (bug) Fixes a bug where the serialization of traceparent headers was not compliant with the W3C specification. This bug was introduced in 2.0.0.

# 2.0.0

- (breaking) Adds OpenTelemetry tracing

# 1.0.6

- (internal) Use the symfony serializer in publisher
- (docs) Improved README.md

# 1.0.5

- (internal) Remove built-in DaprSubscribeController

# 1.0.4

- (internal) Fix code-style
- (internal) Add missing dependency to symfony/framework-bundle `^6.1`

# 1.0.3

- (bug) Fix On handler import statement

# 1.0.2

- (improvement) Add fallback version for illuminate/collections `^8.12`

# 1.0.1

- (docs) Improved README.md
- (internal) Improved overall code 

# 1.0

- (feature) Initial release.