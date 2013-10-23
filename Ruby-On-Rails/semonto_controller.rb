class SemontoController < ApplicationController
  def index
    test = "#{params[:test]}"
    if test == "load-now"
      data = load_data
      process_load(data[0].to_f)
    elsif test == "load-5m"
      data = load_data
      process_load(data[1].to_f)
    elsif test == "load-15m"
      data = load_data
      process_load(data[2].to_f)
    else
      print_result(-1, "0 Not Implemented")
    end
  end
  
  def load_data
    load = File.new('/proc/loadavg').read
    load.split(" ")
  end
  
  def process_load(value)
    if value > 15
      print_result(4, value)
    elsif value > 3
      print_result(1, value)
    else
      print_result(0, value)
    end
  end
  
  def print_result(status, info)
    render text: "#{status}::INFO::#{info}"
  end
end
