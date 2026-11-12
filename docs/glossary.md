# Glossary

Ubiquitous language of the Vending context. Code, tests and docs use these exact terms.

## Concepts

- **Vending Machine**: sells items for coins. Keeps track of items, available change and balance.
- **Customer**: inserts coins, selects items, asks for coins back.
- **Service Operator**: refills items and available change.
- **Money**: an amount expressed in cents: 0.05 is 5 cents, 1 is 100 cents (see D-006).
- **Coin**: accepted values are 0.05, 0.10, 0.25 and 1. The machine only returns 0.05, 0.10 and 0.25.
- **Balance**: money the current customer has inserted and not yet spent or got back.
- **Available Change**: 0.05, 0.10 and 0.25 coins the machine holds for giving change.
- **Cash Box**: where inserted 1 coins go. They are never dispensed (see D-001).
- **Change**: coins returned when the balance is greater than the price.
- **Item**: a product for sale (Water, Juice, Soda), with a selector, a price and a stock.
- **Selector**: the code the customer uses to choose an item (e.g. `WATER`). Unique within a machine.
- **Price**: what an item costs.
- **Stock**: how many units of an item are left. Never negative.
- **Machine Status**: read-only view of items (selector, price, stock), balance and available change.

## Commands, events and exceptions

- **Insert Coin** (Customer)
  - Event: Coin Inserted
  - Exceptions: Invalid Coin; Refund Not Guaranteed (1 coin the machine could not give back, see D-001)
- **Select Item** (Customer)
  - Events: Item Dispensed, plus Change Dispensed when there is change
  - Exceptions: Out of Stock; Insufficient Funds; Change Unavailable
- **Return Coin** (Customer)
  - Event: Coins Returned
- **Service Machine** (Service Operator)
  - Event: Machine Serviced
