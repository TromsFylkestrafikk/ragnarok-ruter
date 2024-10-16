# Change Log

## [Unreleased]

### Changed
- Widened some columns that has potential or actual wider data than
  initially provided.
- Transaction ID is md5-ed due to length > 255, the original ID is
  kept as `id_real`.

## [0.1.0] – 2024-04-18

### Added
- Implemented sink API for stage 1: Raw retrieval (fetch)
- Implemented sink API for stage 2: DB import and removal
