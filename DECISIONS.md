# Decisions

## D-001: The machine never returns 1 coins

- **Decision**: only 0.05, 0.10 and 0.25 are returned, in order not to break the contract
specified in the specs since the domain expert I consulted could not confirm it is a typo.
- **Consequence**: a 1 coin is rejected if the machine can't guarantee returning it in smaller 
coins. The customer never loses money.
- **Discarded**: returning the inserted 1 coin. Simpler, but breaks the spec contract.

## D-002: TDD for domain and application code

- **Decision**: domain and application code is written test-first. Skeleton, config and CLI wiring are tested after.
- **Consequence**: one commit per behaviour, test and code together. Test names describe business behaviour.
- **Discarded**: separate failing-test commits (break CI and `git bisect`); outside-in double-loop TDD from the CLI (reorders the roadmap for little gain).

## D-003: State persisted in SQLite

- **Decision**: state is saved in SQLite after every command, in one transaction, behind the repository port. See [ADR-001](docs/adr/001-persist-state-in-sqlite.md).

## D-004: One bounded context, Vending

- **Decision**: a single bounded context, Vending, in a modular-monolith layout. See [ADR-002](docs/adr/002-one-bounded-context.md).

## D-005: Coins are an enum, Items are data

- **Decision**: accepted coins are a PHP enum; Items (selector, price, stock) are data.
- **Consequence**: Items change at operational speed (Service Machine), coins at release speed 
(new hardware, new rules). A new coin is one enum case.
- **Discarded**: coins as data or as per-machine configuration (only pays off with multiple 
currencies or markets).

## D-006: Amounts in cents

- **Decision**: Money is an integer amount of cents. The spec's values (0.05, 0.10, 0.25) imply
two decimals.
- **Consequence**: exact arithmetic, no floating-point rounding.
- **Discarded**: floats (rounding errors); a currency per amount (one machine, one currency); 
supporting currencies without cents, like yen, now (speculative).

## D-007: Change by backtracking

- **Decision**: the change calculator uses backtracking: try the biggest coins first, undo a choice when stuck, stop at the first exact match.
- **Consequence**: exact change is always found when one exists. Exponential in theory, instant for the few dozen coins a machine holds. Preferring other coins (e.g. the ones with most stock) is a change of order.
- **Discarded**: greedy (fails with limited coins, e.g. 0.30 from one 0.25 and three 0.10); dynamic programming (predictable time, but more code for no gain at this scale).

## D-008: Rejections are domain events

- **Decision**: a rejected command records a domain event with a reason: Coin Rejected (Invalid Coin, Refund Not Guaranteed) or Purchase Rejected (Out of Stock, Insufficient Funds, Change Unavailable). Exceptions are only for broken invariants (bugs).
- **Consequence**: the board maps one-to-one to the code. Every outcome is an event, so output and future reactions (e.g. alerting to refill change) use one model. A new rejection is a new reason.
- **Discarded**: domain exceptions for rejections (expected business outcomes as control flow, and absent from event storming practice).

## D-009: CLI as the interface

- **Decision**: customers and operators use two command-line entry points. See [ADR-003](docs/adr/003-cli-as-the-interface.md).

## D-010: CLI formats

- **Decision**: each input line holds comma-separated actions and gets one `->` output line, as in the spec. SERVICE is `SERVICE, WATER=0.65x10, 0.05x20`: items as `SELECTOR=PRICExSTOCK`, change as `VALUExCOUNT`. Output shows what the machine hands out, plus the reason when it refuses: `0.01 (INVALID COIN)`, `INSUFFICIENT FUNDS`, `SERVICED`. STATUS, not in the spec, shows items, prices and Balance; `bin/service` adds Available Change.
- **Consequence**: the spec's examples run verbatim. A new product is one more `SELECTOR=PRICExSTOCK`. Unknown actions are input errors handled by the CLI, never reaching the domain.
- **Discarded**: a SERVICE config file (JSON/YAML), an extra format to maintain for one line of input; silent rejections (the spec gives no output for them, but a silent refusal leaves the customer guessing).

## D-011: Operators see the Cash Box

- **Decision**: the operator's STATUS ends with the Cash Box (e.g. `CASH BOX 1x5`). Customers don't see it.
- **Consequence**: on a service visit the operator knows what money to collect. It extends D-010, where operator STATUS only added the Available Change.
- **Discarded**: Cash Box hidden (the operator can't see what to collect); Cash Box in the customer STATUS (it's not the customer's information).
