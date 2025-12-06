describe('Purchase Form', () => {


   function doLogin() {
  cy.visit('https://app.invoicing.co/#/login');
  cy.get('input[type="email"]').type('maham@gmail.com');
  cy.get('input[type="password"]').type('nisha2006');
  cy.get('button[type="submit"]').click();
  cy.url().should('include', '/dashboard');
  cy.wait(1000);
  cy.get('svg[viewBox="0 0 12 12"]', { timeout: 2000 })
    .then($svg => {
      if ($svg.length) {
        cy.wrap($svg.first()).click({ force: true });
      }
    });
}

  function submitForm() {
    cy.wait(2000);
    cy.contains('button', 'Save').click({ force: true });
  }

  function selectVendor(value) {
    cy.get('input[data-testid="combobox-input-field"]')
      .click({ force: true })
      .type(value, { force: true });
   
  }

  function fillPartial(value) {
    cy.get('input[type="text"][inputmode="numeric"]').eq(0).clear().type(value);
  }
function fillPONumber(value) {
   cy.get('input[type="text"]').eq(1).clear().type(value);
}


  function fillDiscount(value) {
    cy.get('input[type="text"][inputmode="numeric"]').eq(1).clear().type(value);
  }

  function checkEditExists() {
    cy.contains(/edit/i).should('exist');
  }


  
  let testCounter = 1; // initialize a counter

afterEach(function () {
  const screenshotName = `T${testCounter}`; // T1, T2, T3...
  cy.screenshot(screenshotName, { capture: 'fullPage', overwrite: true });
  testCounter++; // increment for next test
});
  // --------------------
  // Before Each
  // --------------------
  beforeEach(() => {
    doLogin();
    cy.visit('https://app.invoicing.co/#/purchase_orders/create');
    cy.get('body', { timeout: 15000 }).should('exist');
    cy.wait(2000);
  });

  // --------------------
  // Test Cases
  // --------------------
  it('P-1 Select vendor only and check edit exists', () => {
    selectVendor('Nisha');
    submitForm();
    checkEditExists();
  });

  it('P-2 Negative PO number', () => {
    selectVendor('Nisha');
    fillPONumber('-PO123');                     
    submitForm();
    checkEditExists();
  });

  it('P-4 Negative discount value', () => {
    selectVendor('Nisha');
    fillDiscount('-1');                        
    submitForm();
    checkEditExists();
  });

   it('P-5 Negative partial value', () => {
    selectVendor('Nisha');
    fillPartial('-1');                         
    submitForm();
    checkEditExists();
  });

  it.only('P-6 Fill all fields with valid random values', () => {
    selectVendor('Nisha');
    fillPartial('50');                           
    fillPONumber('PO456');                       
    fillDiscount('100');                        
    submitForm();
    checkEditExists();
  });

});
