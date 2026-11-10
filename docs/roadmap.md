# Roadmap

One feature = one branch = one PR, in this order.

1. **Discovery docs**: glossary, decisions, event storming board, CLAUDE.md, this roadmap. Depends on: nothing. Done.
2. **Skeleton**: Docker, Composer, PHPUnit, PHPStan, code style, Deptrac, CI, Makefile. Depends on: nothing. ~2h.
3. **Value objects**: Money (integer cents), Coin, Selector, Item. Depends on: 2. ~2h.
4. **Change calculator**: finds an exact combination of 0.05, 0.10 and 0.25 coins with limited stock (not greedy). Depends on: 3. ~2h.
5. **Insert Coin and Return Coin**: aggregate, balance, Cash Box, D-001 rule, events. Depends on: 4. ~2h.
6. **Select Item**: purchase, change, the three rejections. Depends on: 5. ~3h.
7. **Service Machine**: set stock and available change. Depends on: 5. ~1h.
8. **Application layer**: command handlers, Machine Status query, repository port, in-memory adapter. Depends on: 6, 7. ~2h.
9. **CLI**: actions from the spec plus a read-only STATUS. Acceptance tests with the three spec examples. Depends on: 8. ~3h.
10. **Persistent adapter** (first to cut): state survives between CLI runs. Depends on: 8. ~2h.
11. **README and final check**: how to run, requirements, company name check from a clean clone. Depends on: all. ~1h.

Total: ~20h.
