import request from '@/utils/request';
import Resource from '@/api/resource';

class OperationLogsResource extends Resource {
  constructor() {
    super('operation-logs');
  }
}

export { OperationLogsResource as default };
