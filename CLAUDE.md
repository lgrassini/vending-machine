# CLAUDE.md

Vending machine in PHP, built with DDD and hexagonal architecture.

## Read first (source of truth)

- `DECISIONS.md`: decisions already made. Don't re-litigate them; flag disagreements.
- `docs/glossary.md`: ubiquitous language. Use these exact terms in code, tests and docs.
- `docs/foundations.md`: design principles.
- `docs/event-storming.png`: domain model.
- `docs/roadmap.md`: features and order.

## Workflow

- One feature = one branch off `main` = one PR. Never commit to `main`.
- Small commits, conventional commit messages.
- TDD for domain and application code (D-002): one test at a time, red > green > refactor, then 
commit test and code together.
- Coverage of new code ≥ 80%.
- Run the full test suite and linters before opening a PR.
- PR description: what changed, why, related decision, how it was tested.
- Never merge your own PR. Wait for review before starting the next feature.

## Guardrails

- No scope, abstractions or "nice to haves" beyond the spec and `DECISIONS.md`.
- On any ambiguity not covered by `DECISIONS.md`: stop and ask.
- Never write the name of the company this exercise is for, anywhere (code, docs, commits, PRs).
- Keep code and docs simple and to the point. No bloat. No Markdown tables.
