# ADR-003: CLI as the interface

## Status

Accepted

## Context

The spec defines actions and examples as text (`1, 0.25, GET-SODA` -> `SODA`) but not how users send them. Customer actions are public, like a machine's front panel. SERVICE is not: in the spec, a service person opens the machine to refill it, so it happens on site.

## Decision

Two command-line entry points over the same application handlers:

- `bin/vending` (customer): insert money, GET-<selector>, RETURN-COIN.
- `bin/service` (operator): SERVICE.

Both run on the machine: `bin/vending` behind the front panel, `bin/service` behind the locked service door as defined in the spec "a service person opens the machine". The CLI only parses text into commands and renders events. Operators are not authenticated.

## Consequences

- The spec's examples run verbatim as acceptance tests.
- SERVICE is off the customer surface, but anyone who can run `bin/service` can change items, prices and change.
- Authentication is a known limitation. The enterprise approach: operators authenticate against a central identity provider (OIDC), the operator role is managed there, the application layer checks it through an authorization port, and Machine Serviced records the operator. The domain stays unaware of identity.
- A new interface (e.g. HTTP) is a new adapter calling the same handlers.

## Alternatives considered

- REST API: the spec defines no endpoints; a server and an invented contract for no gain now.
- Local authentication (OS users, PINs, operator lists in configuration): hand-rolled credentials, not fit for production.
- One CLI for every action: SERVICE on the customer surface.
