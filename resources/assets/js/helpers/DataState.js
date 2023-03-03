export default class DataState {
    constructor() {
      this.state = 'idle';
      this.should_log = true;//whether to log to the console
    }
  
    get isIdle() {
      return this.state=='idle'
    }
  
    get isPending() {
      return this.state=='pending'
    }
  
    get isSuccess() {
      return this.state=='success'
    }
  
    get isError() {
      return this.state=='error'
    }
  
    get isEmpty() {
      return this.state=='empty'
    }
  
    idle() {
      this.state='idle'
      if(this.should_log)console.log("state updated to " + this.state)
    }
  
    pending() {
      if(!this.isIdle)this.stateError('pending')
      this.state='pending'
      if(this.should_log)console.log("state updated to " + this.state)
    }
  
    success() {
      if(!this.isPending)this.stateError('success')
      this.state='success'
      if(this.should_log)console.log("state updated to " + this.state)
    }
  
    error() {
      if(!this.isPending)this.stateError('error')
      this.state='error'
      if(this.should_log)console.log("state updated to " + this.state)
    }
  
    empty() {
      this.state='empty'
      if(this.should_log)console.log("state updated to " + this.state)
    }
  
    stateError(to){
      throw('State error. Cannot update state from ' + this.state + ' to ' + to)
    }
    
  }