# Order Processing Service - Test Documentation

This document provides a comprehensive guide for testing the Order Processing Service using Pest PHP testing framework.

## Checklist Before Writing Unit Tests

### Understanding the System
- [x] Identify all components and their responsibilities
- [x] Understand the input/output flow
- [x] Identify dependencies between components
- [x] Analyze boundary conditions and edge cases

### Test Planning
- [x] Create a test strategy for each component
- [x] Identify which methods need unit tests
- [x] Plan mock strategy for external dependencies
- [x] Identify boundary values for testing
- [x] Determine expected outputs for each test scenario

### Test Environment Setup
- [x] Configure Pest/PHPUnit testing framework
- [x] Set up Docker for consistent testing environment
- [x] Configure code coverage tools
- [x] Set up mock libraries (Mockery)

## Test Categories

### OrderProcessingService Tests
- [x] Basic functionality tests
- [x] Order priority update tests
- [x] Order status saving tests
- [x] Exception handling tests

### OrderProcessorFactory Tests
- [x] Proper processor creation for each order type
- [x] Dependency injection tests

### Order Processor Tests
- [x] TypeA processor handling (CSV export)
- [x] TypeB processor handling (API integration)
- [x] TypeC processor handling (Flag-based processing)
- [x] Unknown type handling

### Edge Cases and Integration Tests
- [x] Multiple orders processing
- [x] Boundary condition tests (amount values 150, 200)
- [x] Exception propagation

## Code Coverage Requirements

### Coverage Types
- **Line Coverage**: 90%+ of all lines executed during tests
- **Branch Coverage**: 90%+ of all conditional branches tested
- **Condition Coverage**: 90%+ of all boolean expressions evaluated to both true and false

### Coverage Targets by Component
| Component | Line Coverage | Branch Coverage | Condition Coverage |
|-----------|---------------|----------------|-------------------|
| OrderProcessingService | 95%+ | 95%+ | 95%+ |
| OrderProcessorFactory | 100% | 100% | 100% |
| TypeAOrderProcessor | 95%+ | 95%+ | 95%+ |
| TypeBOrderProcessor | 95%+ | 95%+ | 95%+ |
| TypeCOrderProcessor | 100% | 100% | 100% |
| UnknownTypeOrderProcessor | 100% | 100% | 100% |

## Verifying Test Results

### Automated Verification
- [x] Tests run automatically in Docker container
- [x] Code coverage report generated after tests
- [x] Minimum coverage thresholds enforced
- [x] Test results provide clear pass/fail status

### Manual Verification
- [x] HTML coverage report available for detailed inspection
- [x] Clear test output showing which tests passed/failed
- [x] Tests isolated to prevent side effects
- [x] Mock expectations verified

### Verification Commands
```bash
# Run all tests
docker-compose up -d

# View test results
docker-compose logs

# Generate and view coverage report
composer test:coverage
# Coverage report available at coverage/index.html
```

## Project Structure

```
├── src/                      # Source code
│   └── Classes/              # Application classes
│       ├── OrderProcessor/   # Order processor implementations
│       │   ├── OrderProcessorInterface.php
│       │   ├── OrderProcessorFactory.php
│       │   ├── TypeAOrderProcessor.php
│       │   ├── TypeBOrderProcessor.php
│       │   ├── TypeCOrderProcessor.php
│       │   └── UnknownTypeOrderProcessor.php
│       ├── APIClient.php
│       ├── APIException.php
│       ├── APIResponse.php
│       ├── DatabaseException.php
│       ├── DatabaseService.php
│       ├── DefaultFileSystem.php
│       ├── FileSystemInterface.php
│       ├── Order.php
│       └── OrderProcessingService.php
├── tests/                    # Test files
│   ├── Unit/                 # Unit tests organized by component
│   │   ├── EdgeCases/        # Edge case tests
│   │   ├── Factory/          # Factory tests
│   │   ├── OrderProcessor/   # Order processor tests
│   │   └── OrderProcessingService/ # Service tests
│   ├── Pest.php              # Pest configuration
│   └── phpunit.xml           # PHPUnit configuration
├── docker-compose.yml        # Docker configuration
├── Dockerfile                # Docker image definition
├── composer.json             # PHP dependencies
└── README.md                 # This documentation
```

## Running Tests

### Using Docker (Recommended)
```bash
# Start the testing container
docker-compose up -d

# View test results
docker-compose logs
```

### Without Docker
```bash
# Install dependencies
composer install

# Run tests
composer test

# Run tests with coverage
composer test:coverage
```

## Viewing Code Coverage

After running tests with coverage, an HTML report is generated in the `coverage` directory.
You can open `coverage/index.html` in your browser to view detailed coverage information.
