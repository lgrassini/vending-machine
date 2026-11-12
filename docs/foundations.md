# Foundations

This challenge simulates a real-world scenario where you need to build a maintainable, extensible
system that will evolve over time. While the current requirements are intentionally simple, 
we expect your solution to demonstrate how you would approach building production-ready software 
in a growing codebase.

**Think of this as a foundation that other engineers will build upon, extend, and maintain for 
years.**

## Domain
- Put all business rules inside the VendingMachine aggregate.
- Keep the domain pure (no framework, no I/O, no CLI knowledge).
- New products, new coin denominations, new rules (e.g. “exact change only”, discounts, etc.) 
  should mostly be changes inside the domain or small extensions, not rewrites.
- Ubiquitous Language

## Easy to change where change is likely
- New products: products are data (selector, price, count) loaded via SERVICE, not a hard-coded
  enum. Adding one means no code change.
- New coins: the accepted coins are defined in one place, the Coin value object.
- New change rules: they live in one domain service, the change calculator (D-007).
- New entry points or storage: an HTTP API or a database is just a new adapter, and the domain
  stays untouched.
- Improved security and telemetry: extensible later via a REST API. Handlers don't know the
  interface, authorization fits in the application layer (ADR-003), and telemetry builds on the
  existing domain events and Machine Status query.

## Business rules in one visible place
- Invariants live inside the aggregate.
- Business rejections are domain events; exceptions are only for broken invariants (D-008).
- Domain events are recorded.
- Code names match the event storming language, so a new engineer can map the diagram to the code.

## Guardrails
- Deptrac stops the domain from depending on infrastructure.
- PHPStan, the code style check and coverage run on every PR.

## Tests
- Unit tests for the domain.
- Application tests with an in-memory repository.
- Acceptance tests built from the 3 spec examples.
- Fast, readable, and named after business behaviour.

## Knowledge
- DECISIONS.md records each decision, what we discarded, and why.
- The README covers running the project with a single Docker command.
- A readable git log: conventional commits, no squashing.


