# ADR-001: Persist machine state in SQLite

## Status

Accepted

## Context

The machine's state (stock, Available Change, Cash Box, Balance) must survive restarts, e.g. a power failure. Every command changes several parts of that state, which must be saved together or not at all.

## Decision

Save the state in SQLite after every command, in one transaction, behind the `VendingMachineRepository` port.

## Consequences

- State survives restarts; a command is never half-saved.
- No server to run: one file, built into PHP.
- The domain doesn't know about SQLite; another store is a new adapter.
- Not built for many machines sharing one store; that would need a database server.

## Alternatives considered

- In-memory only: state lost on restart.
- JSON file: hand-rolled transactions and locking.
- Database server: setup and operations for no gain at this scale.
