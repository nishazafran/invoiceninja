describe('Expenses Form Tests', () => {

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
    cy.visit('https://app.invoicing.co/#/expenses/create');
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
    cy.contains('Expense').should('exist');
  });

  it('T-3: Invalid Client only', () => {
    cy.get('input[data-testid="combobox-input-field"]').eq(0).type('C2', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Expense').should('exist');
  });

  it('T-4: Invalid Vendor only', () => {
    cy.get('input[data-testid="combobox-input-field"]').eq(1).type('V2', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Expense').should('exist');
  });

  it('T-5: Invalid Project only', () => {
    cy.get('input[data-testid="combobox-input-field"]').eq(2).type('P2', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Expense').should('exist');
  });

  it('T-6: Invalid Category only', () => {
    cy.get('input[data-testid="combobox-input-field"]').eq(3).type('C2', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Expense').should('exist');
  });

  it('T-7: Amount BVA low', () => {
    cy.get('input[type="text"][inputmode="numeric"]').type('-1', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Expense').should('exist');
  });

  it('T-8: Amount BVA upp', () => {
    cy.get('input[type="text"][inputmode="numeric"]').type('100000000000000', { force: true });
    cy.get('button[type="submit"]').click();
    cy.contains('Expense').should('exist');
  });


});
