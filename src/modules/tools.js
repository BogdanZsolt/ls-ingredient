export const getElement = (selection) => {
  const element = document.querySelector(selection);
  if (element) {
    return element;
  }
  throw new Error(
    `Please check "${selection}" selector, no such elements exists`
  );
};

export const getAllElements = (selection) => {
  const elements = document.querySelectorAll(selection)
  if(elements){
    return elements
  }
  throw new Error(
    `Please check "${selection}" selector, no such elements exists`
  );
}
