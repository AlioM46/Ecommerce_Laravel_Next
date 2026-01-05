categories:
  - method: GET
    path: /Categories

  - method: GET
    path: /Categories/tree
  
  - method: GET
    path: /Categories/${id}

  - method: GET
    path: /Categories/byname/${category}
  
  - method: GET
    path: /categories/top
  
  - method: DELETE
    path: /Categories/${id}
    
  - method: PUT
    path: /Categories/
    
  - method: POST
    path: /Categories

products:
  - method: GET
    path: /Product
  - method: GET
    path: /product/${productId}
  - method: DELETE
    path: /product/${product.id}
  - method: PUT
    path: /product/isActive
    query:
      productId: ${product.id}
      isActive: ${!isProductActive}
  - method: PUT
    path: /product/{productId}
  - method: POST
    path: /product
  - method: GET
    path: /product/order
    query:
      orderedBy: ${selectedFilter} = (enum => enum enProductsOrderedBy : int
{
      case  Latest = 1;
      case  LowToHigh = 2;
      case  HighToLow = 3;
      case  MostLiked = 4;
      case  Discounted = 5;
})
      pageNumber: ${page}
      pageSize: ${itemsPerPage} 
      categoryId: ${categoryId}
      searchQuery: ${query || ""}
      onlyActive: ${onlyActiveProducts}

shein_products:
  - method: GET
    path: /Product/upadte-shein-price
    query:
      productId: ${product.id}
      url: ${product.sheinUrl}
  - method: GET
    path: /Product/upadte-all-shein-price
  - method: POST
    path: /SheinProducts/
    query:
      url: ${encodeURIComponent(sheinUrl)}
      country: ${countryCode.toUpperCase()}
      currency: USD
      language: ar
      max_items_count: 1
      max_items_per_url: 0
      include_size_chart: false

auth:
  - method: POST
    path: /auth/login

  - method: POST
    path: /auth/register

  - method: POST
    path: /auth/logout
    
  - method: POST
    path: /refresh-token/${userId}



Address:
get - /address
post - /address
get - /address/{addressId} 
put - /address/{addressId} 
delete - /address/{addressId}