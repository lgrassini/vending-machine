# ADR-002: One bounded context, Vending

## Status

Accepted

## Context

The spec describes one machine: customers buy items and a Service Operator refills it. Both use the same model and rules. The code must leave room for new contexts (e.g. Payments) without a restructure.

## Decision

A single bounded context, Vending, in a modular-monolith layout: `src/Vending/{Domain,Application,Infrastructure}`. Deptrac enforces the layer rules in every context.

## Consequences

- A new context is a sibling folder with the same layers and rules.
- One deployable; no network calls between contexts.

## Alternatives considered

- One flat `src/`: no room for new contexts.
- A separate Maintenance context: servicing uses the same model and rules.
- Microservices: operational cost for a single context.
