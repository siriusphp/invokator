---
title: Pipelines
---

# Pipelines

This processor has the following characteristics:
1. The parameters are passed the first callable
2. The value returned by each callable is the first and only parameter passed to the next callable
3. All the callables are called in sequence

#### Use case

```php
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Processors\PipelineProcessor;

$invoker = new Invoker($psr11Container);
$processor = new PipelineProcessor($invoker);

$processor->get('tax_report')
          ->add('ImportCsv@taxReport') // this receives a DTO with a file and a user ID, imports it into a table and returns a DTO with the table name and user ID
          ->add('GenerateTaxReport@compileExcelFile') // this receives the DTO returned by the previous callable, returns a DTO with the name of the XLS file and user ID
          ->add('NotifyReportReady@notifyTaxReport'); // this receives the DTO from the previous callable and sends an email

$processor->process('tax_report', new TaxReportDTO('path_to_csv_file', 'user_id') );
```

The example above uses DTOs to pass messages from one step to the next because each callable in the pipeline receives only one argument and most likely there is some "global" data that each step should know about (eg: the client ID for that report)

### Pipelines can be resumed
In some situations pipelines can be interrupted.  There are 2 scenarios for this: 

###### 1. The callable that is executed knows it can be retried in the future 

In this case it can return an instance of `SuggestedRetry` which instructs the pipeline processor to not execute the rest of the callables and return a `PipelinePromise()`

```php
$processor->get('sales_report')
          ->add('SalesReport@prepareData')
          ->add('SalesReport@generateReport')
          ->add('Notification@sendSalesReport')
```

In this case the `SalesReport@generateReport` talks to a 3rd-party API which returns `429 Too Many Requests`.

Sure, the same can be achieved by throwing an exception and re-trying the entire pipeline but if the previous steps are expensive this would save some processing.

###### 2. The callable that is executed knows the next step should be delayed

In this case it can return an instance of `SuggestedResume` which instructs the pipeline processor to not execute the rest of the callables and return a `PipelinePromise()`

The `PipelinePromise` object has a few properties:
- the remaining callables in the pipeline
- the value that should be the starting point when the pipeline is resumed
- the parameters used to initiate the pipeline. Usually each step in the pipeline knows only about the previous result but this might come in handy in some cases
- the recommended delay (in seconds) to be used before resuming

This details can be used to push the execution into a job queue which varies by application, so it's up to you to actually implement it.

[Next: Middlewares](2_3_middlewares.md)
