<?php
/* Author: Dennis Walker, denniswalker@me.com
* add your quickbooks username and password to a creds.php in the parent directory
* define('QBUser', 'user@example.com');
* define('QBPass', 'myPassword');
*/

require 'vendor/autoload.php';
require '../creds.php';

class QBTest extends PHPUnit_Framework_TestCase {
  private $username = QBUser;
  private $password = QBPass;

  protected $screenshotPath = 'receipts/';

  //QBO layout specifics
  protected $url = 'https://qbo.intuit.com/qbo1/login?webredir';
  private $reportNameXPath = "//td[1]";  //Which report in the list to run from.
  private $transactionsXPath = "//tr[@onmouseover='hl(this)']";
  private $olbTransactionXPath = "//tr[@class='olbMatchStatusLine']/td[1]/a[1]";
  private $bodyIFrame = 'bodyframe';


  protected $webDriver;
  protected $session;
  protected $screenshotDir = 'receipts/';
  

  protected function setUp() {
    $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
    $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    $this->webDriver->get($this->url);
  }

  public function testTitle() {
    $this->assertContains('QuickBooks', $this->webDriver->getTitle());
    $this->login();
  }

  public function login() {
    $this->webDriver->findElement(WebDriverBy::id('login'))->sendKeys($this->username);
    $this->webDriver->findElement(WebDriverBy::id('password'))->sendKeys($this->password);
    $this->webDriver->findElement(WebDriverBy::id('LoginButton'))->click();
    // $this->webDriver->wait(30, 500)->until(function ($driver) {
    //   return $driver->getTitle() === 'Homepage - Dennis Walker - QuickBooks Online Simple Start';
    // });
    // $this->assertContains('Redirect to Homepage', $this->webDriver->getTitle());
    $this->goToReport();
  }

  public function goToReport() {
    sleep(3);
    // $this->assertContains('QuickBooks Online', $this->webDriver->getTitle());
    // $this->webDriver->wait(30)->until(WebDriverExpectedCondition::frameToBeAvailableAndSwitchToIt('bodyframe'));
    $this->webDriver->switchTo()->frame($this->bodyIFrame);
    $this->webDriver->findElement(WebDriverBy::id('nav6'))->click();
    sleep(1);
    $this->webDriver->findElement(WebDriverBy::id('nav613'))->click();
    sleep(2);
    // $this->webDriver->switchTo()->frame('bodyframe');
    $this->webDriver->findElement(WebDriverBy::xpath($this->reportNameXPath));
    $this->webDriver->findElement(WebDriverBy::id('generate'))->click();
    $this->iterateThroughResults();
  }

  public function iterateThroughResults() {
    $numTransactions = count($this->webDriver->findElements(WebDriverBy::xpath($this->transactionsXPath)));
    for ($i=1; $i<=$numTransactions; $i++) {
      $this->webDriver->manage()->window()->setSize(new WebDriverDimension(1000, 1200));
      $this->webDriver->findElement(WebDriverBy::xpath("($this->transactionsXPath)[$i]"))->click();
      sleep(1);
      $this->webDriver->findElement(WebDriverBy::xpath($this->olbTransactionXPath))->click();
      $this->webDriver->manage()->window()->setSize(new WebDriverDimension(1000, 350));
      $this->webDriver->findElement(WebDriverBy::xpath("(//td[@class='contentInnerTD'])[1]"))->sendKeys("\xEE\x80\x95");
      $this->webDriver->findElement(WebDriverBy::xpath("(//td[@class='contentInnerTD'])[1]"))->sendKeys("\xEE\x80\x95");
      sleep(1);
      $screenshotName = $this->screenshotDir . "$i" . ".png";
      $this->webDriver->takeScreenshot($screenshotName);
      $this->webDriver->navigate()->back();
      $this->webDriver->navigate()->back();
      sleep(1);
    }
  }

  public function tearDown() {
    $this->webDriver->manage()->deleteAllCookies();
    $this->webDriver->quit();
  }
}




