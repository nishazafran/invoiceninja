describe('Transactions Form Tests', () => {

   function doLogin() {
  cy.visit('https://app.invoicing.co/#/login');
  cy.get('input[type="email"]').type('neeshaa@gmail.com');
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

  beforeEach(() => {
    doLogin();
    cy.wait(1000);
    cy.visit('https://app.invoicing.co/#/transactions/create');
    cy.wait(4500);
  });


  
  let testCounter = 1; // initialize a counter

afterEach(function () {
  const screenshotName = `T${testCounter}`; // T1, T2, T3...
  cy.screenshot(screenshotName, { capture: 'fullPage', overwrite: true });
  testCounter++; // increment for next test
});


  it('T-1: All fields empty', () => {
    cy.get('button[type="submit"]').click();
    cy.contains('The bank integration id field is required.').should('exist');
  });

  it('T-2: All fields valid', () => {
    cy.get('input[type="date"]').type('2025-12-01', { force: true }); // Date
    cy.get('input[type="text"][inputmode="numeric"]').eq(0).type('50', { force: true }); // Amount
    cy.get('input[data-testid="combobox-input-field"]').type('allied{enter}', { force: true }); // Bank Account
    cy.get('textarea').type('Transaction description', { force: true }); // Description
    cy.get('button[type="submit"]').click();
    cy.contains('Transaction').should('exist');
  });

  it('T-3: Invalid Type', () => {
  cy.get('.css-ood9ll-singleValue').click({ force: true, multiple: true });
 cy.get('button[type="submit"]').click();
    cy.contains('Transaction').should('exist');
  });

  it('T-4: Amount -1 (BVA)', () => {
    cy.get('input[type="text"][inputmode="numeric"]').eq(0).type('-1', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('The bank integration id field is required.').should('exist');
  });

  it('T-5: Amount 101 (BVA)', () => {
    cy.get('input[type="text"][inputmode="numeric"]').eq(0).type('100000000000000', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('The bank integration id field is required.').should('exist');
  });

  it('T-6: Invalid Bank Account', () => {
    cy.get('input[data-testid="combobox-input-field"]').type('xyz', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('The bank integration id field is required.').should('exist');
  });

});
