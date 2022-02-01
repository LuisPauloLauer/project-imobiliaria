import requestExternal from '@/utils/request-external';

class OthersApiRequest {
  // constructor() {
  //  super('users');
  // }
  searchZipCodeBrazil(zipcode) {
    return requestExternal({
      url: 'https://viacep.com.br/ws/' + zipcode + '/json/',
      method: 'get',
    });
  }
}

export { OthersApiRequest as default };
