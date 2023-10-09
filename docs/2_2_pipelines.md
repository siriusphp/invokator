---
title: Pipelines
---

# The pipeline locator

This locator has the following characteristics:
1. The parameters are passed the first callable
2. The value returned by each callable is the first and only parameter passed to the next callable
3. All the callables are called in sequence

#### Use case

```php
use Sirius\StackRunner\Invoker;
use Sirius\StackRunner\Locators\PipelineLocator;

$invoker = new Invoker($psr11Container);
$locator = new PipelineLocator($invoker);

$locator->get('tax_report')
        ->add('ImportCsv@taxReport') // this receives a DTO with a file and a user ID, imports it into a table and returns a DTO with the table name and user ID
        ->add('GenerateTaxReport@compileExcelFile') // this receives the DTO returned by the previous callable, returns a DTO with the name of the XLS file and user ID
        ->add('NotifyReportReady@notifyTaxReport') // this receive the DTO from the previous callable and sends an email

$locator->process('tax_report', new TaxReportDTO('path_to_csv_file', 'user_id') );
```

[Next: Middlewares](2_3_middlewares.md)
