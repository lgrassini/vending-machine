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

- **Decision**: the machine's state is saved in SQLite after every command, behind the repository port.
- **Consequence**: state (stock, change, Cash Box, balance) survives restarts (ie: a power failure). Each command's changes are saved in one SQLite transaction.
- **Discarded**: in-memory only (state lost on restart); JSON file (hand-rolled transactions and locking); database server (unneeded setup).

## D-004: One bounded context, Vending

- **Decision**: a single bounded context, Vending, in a modular-monolith layout: `src/Vending/{Domain,Application,Infrastructure}`.
- **Consequence**: a new context (e.g. Payments) is a sibling folder. Deptrac applies the layer rules to every context.
- **Discarded**: one flat `src/` (no room for new contexts); a separate Maintenance context (servicing uses the same model and rules).

## D-005: Coins are an enum, Items are data

- **Decision**: accepted coins are a PHP enum; Items (selector, price, stock) are data.
- **Consequence**: Items change at operational speed (Service Machine), coins at release speed 
(new hardware, new rules). A new coin is one enum case.
- **Discarded**: coins as data or as per-machine configuration (only pays off with multiple 
currencies or markets).
