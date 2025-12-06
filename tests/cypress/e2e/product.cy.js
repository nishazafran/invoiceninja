describe("Product Form", () => {

  function doLogin() {
  cy.visit('https://app.invoicing.co/#/login');
  cy.get('input[type="email"]').type('haifayousaf11@gmail.com');
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
    cy.contains("button", "Save").click({ force: true });
  }

  
  function selectTaxCategory(value) {
    cy.get('input[role="combobox"]').click({ force: true })
    cy.get('input[role="combobox"]').type(value + "{enter}", { force: true });
  }

 

  beforeEach(() => {
    cy.visit('https://app.invoicing.co/#/login');
    doLogin();
    cy.url({ timeout: 20000 }).should('include', '/dashboard');
    cy.visit('https://app.invoicing.co/#/products/create');
    cy.get('body', { timeout: 15000 }).should('exist'); 
    cy.wait(2000); 
  });


  
  let testCounter = 1; // initialize a counter

afterEach(function () {
  const screenshotName = `T${testCounter}`; // T1, T2, T3...
  cy.screenshot(screenshotName, { capture: 'fullPage', overwrite: true });
  testCounter++; // increment for next test
});
  it("P-1 Should submit with ALL fields empty", () => {
    submitForm();
    cy.contains(/success/i, { timeout: 5000 }).should("exist");
  });

 
  
  it("P-2 Should fill ALL fields with valid values and submit", () => {
    cy.get('input[type="text"]').eq(0).type("Laptop");
    cy.get("textarea").eq(0).type("High-end business laptop");
    cy.get('input[inputmode="numeric"]').eq(0).type("100");
    cy.get('input[inputmode="numeric"]').eq(1).type("70");
    cy.get('input[inputmode="numeric"]').eq(2).type("80");
    selectTaxCategory("Standard");
    cy.get('input[type="text"]').eq(1).type("https://example.com/laptop.png");
    submitForm();
    cy.contains(/success/i, { timeout: 5000 }).should("exist");
  });

 it("P-3 Negative inputs 1", () => {
    cy.get('input[type="text"]').eq(0).type("Laptop");
    cy.get("textarea").eq(0).type("High-end business laptop");
    cy.get('input[inputmode="numeric"]').eq(0).type("-1");
    selectTaxCategory("Standard");
    cy.get('input[type="text"]').eq(1).type("https://example.com/laptop.png");
    submitForm();
    cy.contains(/success/i, { timeout: 5000 }).should("exist");
  });
  
  it("P-4 Upper Boundary", () => {
    cy.get('input[type="text"]').eq(0).type("Laptop");
    cy.get("textarea").eq(0).type("High-end business laptop");
    cy.get('input[inputmode="numeric"]').eq(0).type("101");
    selectTaxCategory("Standard");
    cy.get('input[type="text"]').eq(1).type("https://example.com/laptop.png");
    submitForm();
    cy.contains(/success/i, { timeout: 5000 }).should("exist");
  });
  


  it("P-5 Negative Value 2", () => {
    cy.get('input[type="text"]').eq(0).type("Laptop");
    cy.get("textarea").eq(0).type("High-end business laptop");
    cy.get('input[inputmode="numeric"]').eq(1).type("-1");
    selectTaxCategory("Standard");
    cy.get('input[type="text"]').eq(1).type("https://example.com/laptop.png");
    submitForm();
    cy.contains(/success/i, { timeout: 5000 }).should("exist");
  });

  
  it("P-6 Upper Boundary", () => {
    cy.get('input[type="text"]').eq(0).type("Laptop");
    cy.get("textarea").eq(0).type("High-end business laptop");
    cy.get('input[inputmode="numeric"]').eq(1).type("101");
    selectTaxCategory("Standard");
    cy.get('input[type="text"]').eq(1).type("https://example.com/laptop.png");
    submitForm();
    cy.contains(/success/i, { timeout: 5000 }).should("exist");
  });

  
  it("P-7 Negative Value 3", () => {
    cy.get('input[type="text"]').eq(0).type("Laptop");
    cy.get("textarea").eq(0).type("High-end business laptop");
    cy.get('input[inputmode="numeric"]').eq(2).type("-1");
    selectTaxCategory("Standard");
    cy.get('input[type="text"]').eq(1).type("https://example.com/laptop.png");
    submitForm();
    cy.contains(/success/i, { timeout: 5000 }).should("exist");
  });

  
  it("P-8 Upper Boundary", () => {
    cy.get('input[type="text"]').eq(0).type("Laptop");
    cy.get("textarea").eq(0).type("High-end business laptop");
    cy.get('input[inputmode="numeric"]').eq(2).type("101");
    selectTaxCategory("Standard");
    cy.get('input[type="text"]').eq(1).type("https://example.com/laptop.png");
    submitForm();
    cy.contains(/success/i, { timeout: 5000 }).should("exist");
  });


 
  it("P-9 Should test with item and url", () => {
    cy.get('input[type="text"]').eq(0).type("Test Item");
    cy.get('input[type="text"]').eq(1).type("https://example.com/test.png");
    submitForm();
    cy.contains(/success/i, { timeout: 5000 }).should("exist");
  });

});
