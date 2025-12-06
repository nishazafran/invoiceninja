describe('Invoice Form', () => {

  // --------------------
  // Helper functions
   function doLogin() {
  cy.visit('https://app.invoicing.co/#/login');
  cy.get('input[type="email"]').type('nisha@gmail.com');
  cy.get('input[type="password"]').type('nisha2005');
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

  function selectClient(value) {
    cy.get('input[data-testid="combobox-input-field"]')
      .click({ force: true })
      .type(value, { force: true });
  }

  function selectDiscount(value) {
    cy.get('.css-b62m3t-container').first().click({ force: true });
    cy.get('input[id^="react-select-"]').first().type(value + '{enter}', { force: true });
  }

  function fillPartial(value) {
    cy.get('input[type="text"][inputmode="numeric"]').eq(0).clear().type(value);
  }

  function fillSecondDiscount(value) {
    cy.get('input[type="text"][inputmode="numeric"]').eq(1).clear().type(value);
  }

  function checkEditExists() {
    cy.contains(/edit/i).should('exist');
  }

  // --------------------
  // Before Each
  // --------------------
  beforeEach(() => {
    doLogin();
    cy.visit('https://app.invoicing.co/#/invoices/create');
    cy.get('body', { timeout: 15000 }).should('exist');
    cy.wait(2000);
  });


  
  let testCounter = 1; // initialize a counter

afterEach(function () {
  const screenshotName = `T${testCounter}`; // T1, T2, T3...
  cy.screenshot(screenshotName, { capture: 'fullPage', overwrite: true });
  testCounter++; // increment for next test
});
  // --------------------
  // Test Cases
  // --------------------

  it('P-1 Select client only and check edit exists', () => {
    selectClient('nisha');
    submitForm();
    checkEditExists();
  });

  it('P-2 Due date', () => {
    selectClient('nisha');
    cy.get('input[type="date"]').first().type('2025-11-30'); 
    cy.get('input[type="date"]').eq(1).type('2025-11-30');  
    submitForm();
    checkEditExists();
  });

 it('P-3 Negative invoice number with invoice & due date', () => {
    selectClient('nisha');
    cy.get('input[type="date"]').first().type('2025-11-30'); 
    cy.get('input[type="date"]').eq(1).type('2025-11-30');   
    cy.get('#number').type('-INV123');                           
    submitForm();
    checkEditExists();
  });

  it('P-4 Negative PO number', () => {
    selectClient('nisha');
    cy.get('#po_number').type('-PO123');                    
    submitForm();
    checkEditExists();
  });

  it('P-5 Negative discount value', () => {
    selectClient('nisha');
    selectDiscount('-1');                                                             
    submitForm();
    checkEditExists();
  });

  
  it('P-6 Negative 2nd discount value', () => {
    selectClient('nisha');;                                   
    fillSecondDiscount('-1');                               
    submitForm();
    checkEditExists();
  });

  

  it('P-7 Fill all fields with valid random values', () => {
    selectClient('nisha');
    cy.get('input[type="date"]').first().type('2025-11-30'); // Invoice date
    cy.get('input[type="date"]').eq(1).type('2025-12-15');   // Due date
    fillPartial('50');                                       // Partial text box
    cy.get('#number').type('INV123');                        // Invoice number
    cy.get('#po_number').type('PO456');                      // PO number
    selectDiscount('Amount');                                 // Discount dropdown
    fillSecondDiscount('100');                                // Second discount box
    submitForm();
    checkEditExists();
  });


  it('P-8 Fill all fields with valid random values', () => {                            
    fillSecondDiscount('-1');                              
    submitForm();
    checkEditExists();
  });

});
